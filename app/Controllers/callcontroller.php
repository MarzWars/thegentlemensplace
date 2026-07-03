<?php
// app/Controllers/CallController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF};
use App\Models\{Performer, CallLink, Call, User};
use App\Services\{CreditService, LiveKitService, PusherService};

class CallController extends Controller
{
    private const MIN_CREDITS = 5; // must have at least 5 credits to initiate

    /**
     * Client requests a voice call with a performer.
     */
    public function request(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $userId      = (int)$_SESSION['user_id'];
        $performerId = (int)($_POST['performer_id'] ?? 0);

        $performerModel = new Performer();
        $performer = $performerModel->find($performerId);

        if (!$performer || $performer['status'] !== 'active') {
            $this->abort(404, 'Performer not found.');
        }

        // Determine call type
        $callType = isset($_POST['call_type']) && $_POST['call_type'] === 'video' ? 'video' : 'voice';

        // Check if video calls are enabled for this performer if video requested
        if ($callType === 'video' && !$performer['video_enabled']) {
            $this->flashError($performer['display_name'] . ' does not support video calls.');
            header('Location: ' . BASE_PATH . '/performer/' . $performer['slug']);
            return;
        }

        // Calculate minimum credits required based on call type
        $minCreditsRequired = $callType === 'video'
            ? (float)$performer['video_min_credits']
            : self::MIN_CREDITS;

        $creditService = new CreditService();
        $balance = $creditService->getBalance($userId);

        if ($balance < $minCreditsRequired) {
            $this->flashError('You need at least ' . $minCreditsRequired . ' credits to connect.');
            header('Location: ' . BASE_PATH . '/credits');
            return;
        }

        $dbInstance = \App\Config\Database::getInstance();
        $settingStmt = $dbInstance->prepare("SELECT `value` FROM settings WHERE `key` = 'admin_proxy_mode' LIMIT 1");
        $settingStmt->execute();
        $proxyMode = $settingStmt->fetchColumn() === '1';

        if (!$performer['online_status'] && !$proxyMode) {
            $this->flashError($performer['display_name'] . ' is currently offline.');
            header('Location: ' . BASE_PATH . '/performer/' . $performer['slug']);
            return;
        }

        // Generate call session and link
        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        $linkModel = new CallLink();
        $linkId = $linkModel->create([
            'token'        => $token,
            'user_id'      => $userId,
            'performer_id' => $performerId,
            'expires_at'   => $expires,
            'ip_created'   => $_SERVER['REMOTE_ADDR'],
        ]);

        $callUuid  = \App\Models\User::generateUuid();
        $callModel = new Call();

        $rate = $callType === 'video' ? $performer['video_rate_per_minute'] : $performer['rate_per_minute'];
        $minCredits = $callType === 'video' ? $performer['video_min_credits'] : 0.00;
        $minMinutes = $callType === 'video' ? $performer['video_min_minutes'] : 0;

        $callId    = $callModel->create([
            'uuid'            => $callUuid,
            'type'            => $callType,
            'call_link_id'    => $linkId,
            'user_id'         => $userId,
            'performer_id'    => $performerId,
            'status'          => 'ringing',
            'rate_per_minute' => $rate,
            'min_credits'     => $minCredits,
            'min_minutes'     => $minMinutes,
        ]);

        // Get user details
        $userModel = new User();
        $user = $userModel->findById($userId);
        $callerName = $user['username'] ?? 'Client';

        // Notify Performer in real-time using Pusher Beams
        $pusher = new PusherService();
        $pusher->sendCallNotification($performerId, $callerName, $callUuid);

        // Redirect client to the ringing screen
        header('Location: ' . BASE_PATH . '/call/ringing/' . $callUuid);
    }

    /**
     * Client call-ringing loading page.
     */
    public function ringing(string $uuid): void
    {
        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call) {
            $this->abort(404, 'Call session not found.');
        }

        if ((int)$call['user_id'] !== (int)$_SESSION['user_id']) {
            $this->abort(403, 'Unauthorized.');
        }

        $performerModel = new Performer();
        $performer = $performerModel->find($call['performer_id']);

        $this->view('calls/calling', [
            'title'     => 'Calling ' . $performer['display_name'] . '...',
            'performer' => $performer,
            'call'      => $call,
        ]);
    }

    /**
     * JSON Endpoint: check current status of a call session (no auth — UUID is the secret)
     */
    public function status(string $uuid): void
    {
        // Force JSON response regardless of session state
        header('Content-Type: application/json');
        header('Cache-Control: no-store');

        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call) {
            echo json_encode(['status' => 'not_found']);
            exit;
        }

        echo json_encode([
            'id'     => $call['id'],
            'status' => $call['status']
        ]);
        exit;
    }

    /**
     * Performer Endpoint: Accept the call
     */
    public function accept(string $uuid): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $performerId = (int)$_SESSION['performer_id'];
        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call || (int)$call['performer_id'] !== $performerId) {
            $this->json(['success' => false, 'message' => 'Unauthorized or call not found.'], 403);
            return;
        }

        // Mark as accepted AND immediately answered so the client poll catches it
        $callModel->updateStatus($call['id'], 'accepted');
        $callModel->markAnswered($call['id']);

        $this->json([
            'success'  => true,
            'room_url' => BASE_PATH . '/call/room/' . $uuid
        ]);
    }

    /**
     * Performer Endpoint: Decline the call
     */
    public function decline(string $uuid): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $performerId = (int)$_SESSION['performer_id'];
        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call || (int)$call['performer_id'] !== $performerId) {
            $this->json(['success' => false, 'message' => 'Unauthorized or call not found.'], 403);
            return;
        }

        // Update status to declined
        $callModel->updateStatus($call['id'], 'declined', 'declined_by_performer');

        $this->json(['success' => true]);
    }

    /**
     * WebRTC LiveKit Room UI page (accessible by both client and performer)
     */
    public function room(string $uuid): void
    {
        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call) {
            $this->abort(404, 'Call session not found.');
        }

        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $performerId = isset($_SESSION['performer_id']) ? (int)$_SESSION['performer_id'] : 0;

        $isUser = ($userId === (int)$call['user_id']);
        $isPerformer = ($performerId === (int)$call['performer_id']);

        if (!$isUser && !$isPerformer) {
            $this->abort(403, 'You are not authorized to join this room.');
        }

        // Set status to in_progress if we are just joining/starting
        if ($call['status'] === 'accepted') {
            $callModel->markAnswered($call['id']);
        }

        // Generate LiveKit token
        $liveKit = new LiveKitService();
        $identity = $isUser ? 'user_' . $userId : 'performer_' . $performerId;
        $displayName = $isUser ? ($_SESSION['username'] ?? 'Client') : 'Performer';
        $token = $liveKit->generateToken($uuid, $identity, $displayName);

        $performerModel = new Performer();
        $performer = $performerModel->find($call['performer_id']);

        $this->view('calls/room', [
            'title'        => $call['type'] === 'video' ? 'Video Call Room' : 'Voice Call Room',
            'call'         => $call,
            'performer'    => $performer,
            'livekitToken' => $token,
            'livekitUrl'   => LIVEKIT_URL,
            'isPerformer'  => $isPerformer,
        ]);
    }

    /**
     * Performer or Client Endpoint: End the call and finalize billing
     */
    public function end(string $uuid): void
    {
        header('Content-Type: application/json');

        $callModel = new Call();
        $call = $callModel->findByUuid($uuid);

        if (!$call) {
            echo json_encode(['success' => false]);
            exit;
        }

        // Only finalize if currently active
        if (in_array($call['status'], ['in_progress', 'accepted', 'ringing'])) {
            $durationSecs = 0;
            if (!empty($call['answered_at'])) {
                $db = \App\Config\Database::getInstance();
                $stmt = $db->prepare("SELECT TIMESTAMPDIFF(SECOND, answered_at, NOW()) FROM calls WHERE id = ?");
                $stmt->execute([$call['id']]);
                $durationSecs = max(0, (int)$stmt->fetchColumn());
            }

            $minutesBilled   = ceil($durationSecs / 60);
            
            if ($call['type'] === 'video') {
                $minMins = (int)$call['min_minutes'];
                if ($minutesBilled <= $minMins) {
                    $totalCredits = (float)$call['min_credits'];
                } else {
                    $totalCredits = (float)$call['min_credits'] + ($minutesBilled - $minMins) * (float)$call['rate_per_minute'];
                }
            } else {
                $totalCredits = $minutesBilled * (float)$call['rate_per_minute'];
            }

            $performerModel  = new Performer();
            $performer       = $performerModel->find($call['performer_id']);
            $commissionRate  = (float)($performer['commission_rate'] ?? 70) / 100;
            $performerEarns  = round($totalCredits * $commissionRate, 4);
            $platformEarns   = round($totalCredits - $performerEarns, 4);

            $callModel->finalize($call['id'], [
                'status'             => 'completed',
                'duration_seconds'   => $durationSecs,
                'credits_used'       => $totalCredits,
                'performer_earnings' => $performerEarns,
                'platform_earnings'  => $platformEarns,
                'ended_at'           => date('Y-m-d H:i:s'),
                'termination_reason' => 'completed',
            ]);

            // Note: Performer earnings are already credited in real-time minute-by-minute via tickBilling().
            // We do not add them again here to avoid double-crediting.
            
            if ($durationSecs > 0) {
                $performerModel->incrementCallStats($call['performer_id'], $durationSecs);
            }
        }

        echo json_encode(['success' => true]);
        exit;
    }


    public function tickBilling(): void
    {
        $userId = (int)$_SESSION['user_id'];
        $callUuid = $_POST['call_uuid'] ?? '';

        $db = \App\Config\Database::getInstance();

        $callModel = new Call();
        $call = $callModel->findByUuid($callUuid);

        if (!$call || $call['status'] !== 'in_progress') {
            $this->json(['success' => false, 'message' => 'Call is not active.'], 400);
            return;
        }

        $performerModel = new Performer();
        $performer = $performerModel->find($call['performer_id']);

        $creditService = new CreditService();
        $balance = $creditService->getBalance($userId);

        $deductAmount = 0.00;
        
        if ($call['type'] === 'video') {
            $stmt = $db->prepare("SELECT TIMESTAMPDIFF(SECOND, answered_at, NOW()) FROM calls WHERE id = ?");
            $stmt->execute([$call['id']]);
            $elapsedSeconds = max(0, (int)$stmt->fetchColumn());
            $elapsedMinutes = floor($elapsedSeconds / 60);

            if ($elapsedMinutes == 0) {
                $deductAmount = (float)$call['min_credits'];
            } elseif ($elapsedMinutes < (int)$call['min_minutes']) {
                $deductAmount = 0.00;
            } else {
                $deductAmount = (float)$call['rate_per_minute'];
            }
        } else {
            $deductAmount = (float)$call['rate_per_minute'];
        }

        if ($deductAmount > 0) {
            if ($balance < $deductAmount) {
                $callModel->updateStatus($call['id'], 'completed', 'out_of_credits');
                $this->json(['success' => false, 'message' => 'out_of_credits'], 402);
                return;
            }

            $newBalance = $creditService->deductCallCredits($userId, $call['id'], $deductAmount);
            
            $commissionRate = (float)$performer['commission_rate'] / 100;
            $performerEarns = round($deductAmount * $commissionRate, 4);
            $performerModel->addEarnings($performer['id'], $performerEarns);
        } else {
            $newBalance = $balance;
        }

        $this->json([
            'success'     => true,
            'new_balance' => $newBalance,
        ]);
    }

    /**
     * JSON Endpoint: Poll for active incoming calls (Fallback/supplement to push notifications)
     */
    public function checkIncoming(): void
    {
        if (empty($_SESSION['performer_id'])) {
            $this->json(['success' => false], 401);
            return;
        }

        $performerId = (int)$_SESSION['performer_id'];
        $db = \App\Config\Database::getInstance();

        // Find active call session in the last 60 seconds with status 'ringing'
        $stmt = $db->prepare("
            SELECT c.uuid, u.username, c.type
            FROM calls c
            JOIN users u ON c.user_id = u.id
            WHERE c.performer_id = ? AND c.status = 'ringing' AND c.created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
            ORDER BY c.created_at DESC
            LIMIT 1
        ");
        $stmt->execute([$performerId]);
        $incoming = $stmt->fetch();

        if ($incoming) {
            $this->json([
                'incoming' => true,
                'uuid' => $incoming['uuid'],
                'username' => $incoming['username'],
                'type' => $incoming['type']
            ]);
        } else {
            $this->json(['incoming' => false]);
        }
    }
}
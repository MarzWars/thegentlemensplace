<?php
// app/Controllers/StreamController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF};
use App\Models\{Performer, Stream, User};
use App\Services\CreditService;

class StreamController extends Controller
{
    private const MIN_STREAM_CREDITS = 10; // Must have at least 10 credits to watch a premium stream

    /**
     * Display the streaming view for a performer (OBS settings or WebRTC broadcaster client)
     */
    public function broadcast(): void
    {
        if (empty($_SESSION['performer_id'])) {
            $this->redirect('login');
            return;
        }

        $performerId = (int)$_SESSION['performer_id'];
        $performerModel = new Performer();
        $performer = $performerModel->find($performerId);

        $streamModel = new Stream();
        $activeStream = $streamModel->findActiveByPerformer($performerId);

        $this->view('performer-dash/broadcast', [
            'title'        => 'Live Broadcaster Dashboard',
            'performer'    => $performer,
            'activeStream' => $activeStream,
        ]);
    }

    /**
     * Start the stream session (API)
     */
    public function start(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        if (empty($_SESSION['performer_id'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $performerId = (int)$_SESSION['performer_id'];
        $title = trim($_POST['title'] ?? 'Live Session');

        $streamModel = new Stream();
        $existing = $streamModel->findActiveByPerformer($performerId);

        if ($existing) {
            $this->json(['success' => true, 'stream' => $existing]);
            return;
        }

        // Setup Agora / Twilio Live / WebRTC signaling details
        $channelName = 'stream_' . $performerId . '_' . bin2hex(random_bytes(4));
        $uuid = \App\Models\User::generateUuid();

        // In a real implementation, you would call a service to generate an Agora Token here
        // e.g. $token = AgoraTokenBuilder::buildRtcTokenWithUid(...);
        $mockToken = 'mock_agora_token_' . bin2hex(random_bytes(16));

        $streamId = $streamModel->create([
            'uuid'         => $uuid,
            'performer_id' => $performerId,
            'title'        => $title,
            'channel_name' => $channelName,
            'token'        => $mockToken,
        ]);

        $stream = $streamModel->find($streamId);

        // Update performer online status to 'streaming' or online
        $db = \App\Config\Database::getInstance();
        $db->prepare("UPDATE performers SET online_status = 1 WHERE id = ?")->execute([$performerId]);

        $this->json([
            'success' => true,
            'stream'  => $stream,
            'app_id'  => 'AGORA_APP_ID_PLACEHOLDER' // We'll configure this on production
        ]);
    }

    /**
     * End the stream session (API)
     */
    public function end(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        if (empty($_SESSION['performer_id'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $performerId = (int)$_SESSION['performer_id'];
        $streamModel = new Stream();
        $active = $streamModel->findActiveByPerformer($performerId);

        if ($active) {
            $streamModel->endStream($active['id']);
        }

        $this->json(['success' => true]);
    }

    /**
     * View a performer's stream (for standard logged-in users)
     */
    public function watch(string $slug): void
    {
        $performerModel = new Performer();
        $performer = $performerModel->findBySlug($slug);

        if (!$performer || $performer['status'] !== 'active') {
            $this->abort(404, 'Performer not found');
        }

        $streamModel = new Stream();
        $stream = $streamModel->findActiveByPerformer($performer['id']);

        if (!$stream) {
            $this->view('streams/offline', [
                'title'     => $performer['display_name'] . ' is Offline',
                'performer' => $performer,
            ]);
            return;
        }

        $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $hasCredits = false;
        $balance = 0.0;

        if ($userId > 0) {
            $creditService = new CreditService();
            $balance = $creditService->getBalance($userId);
            $hasCredits = ($balance >= self::MIN_STREAM_CREDITS);
        }

        $this->view('streams/watch', [
            'title'      => 'Watching ' . $performer['display_name'] . ' Live!',
            'performer'  => $performer,
            'stream'     => $stream,
            'hasCredits' => $hasCredits,
            'balance'    => $balance,
        ]);
    }

    /**
     * Periodic billing tick for stream viewer (API called every minute)
     */
    public function tickBilling(): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $streamUuid = $_POST['stream_uuid'] ?? '';

        $streamModel = new Stream();
        $stream = $streamModel->findActiveByUuid($streamUuid);

        if (!$stream) {
            $this->json(['success' => false, 'message' => 'Stream is no longer live.'], 404);
            return;
        }

        $performerModel = new Performer();
        $performer = $performerModel->find($stream['performer_id']);

        // Streams could have a set stream rate or fall back to their calling rate (e.g. 50% of the calling rate)
        $streamRate = round((float)$performer['rate_per_minute'] * 0.5, 2); // 50% rate for public streaming

        $creditService = new CreditService();
        // Check balance
        $balance = $creditService->getBalance($userId);
        if ($balance < $streamRate) {
            $this->json(['success' => false, 'message' => 'Insufficient credits.'], 402);
            return;
        }

        // Deduct credits and credit performer
        $creditService->deductCallCredits($userId, null, $streamRate); // Reuse credit deduction logic
        
        $commissionRate = (float)$performer['commission_rate'] / 100;
        $performerEarns = round($streamRate * $commissionRate, 4);
        $performerModel->addEarnings($performer['id'], $performerEarns);

        $newBalance = $creditService->getBalance($userId);

        $this->json([
            'success'     => true,
            'new_balance' => $newBalance,
            'rate'        => $streamRate
        ]);
    }

    /**
     * Send a tip to the streaming performer
     */
    public function tip(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        if (empty($_SESSION['user_id'])) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
            return;
        }

        $userId = (int)$_SESSION['user_id'];
        $performerId = (int)($_POST['performer_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);

        if ($amount <= 0) {
            $this->json(['success' => false, 'message' => 'Invalid tip amount.'], 400);
            return;
        }

        $creditService = new CreditService();
        $balance = $creditService->getBalance($userId);

        if ($balance < $amount) {
            $this->json(['success' => false, 'message' => 'Insufficient credits for tip.'], 402);
            return;
        }

        // Deduct tip amount
        $creditService->deductCallCredits($userId, null, $amount);

        // Credit performer
        $performerModel = new Performer();
        $performer = $performerModel->find($performerId);
        if (!$performer) {
            $this->json(['success' => false, 'message' => 'Performer not found.'], 404);
            return;
        }

        $commissionRate = (float)$performer['commission_rate'] / 100;
        $performerEarns = round($amount * $commissionRate, 4);
        $performerModel->addEarnings($performerId, $performerEarns);

        $newBalance = $creditService->getBalance($userId);

        $this->json([
            'success'     => true,
            'message'     => 'Tip sent successfully!',
            'new_balance' => $newBalance
        ]);
    }
}

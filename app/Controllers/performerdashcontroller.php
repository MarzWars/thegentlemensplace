<?php
// app/Controllers/PerformerDashController.php
namespace App\Controllers;

use App\Core\{Controller, CSRF};
use App\Config\Database;
use App\Models\Call;

class PerformerDashController extends Controller
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function dashboard(): void
    {
        $performerId = (int)$_SESSION['performer_id'];
        $callModel   = new Call();

        $stmt = $this->db->prepare("SELECT * FROM performers WHERE id = ?");
        $stmt->execute([$performerId]);
        $performer = $stmt->fetch();

        $this->view('performer-dash/dashboard', [
            'title'       => 'Performer Dashboard',
            'performer'   => $performer,
            'recentCalls' => $callModel->getRecentForPerformer($performerId, 10),
            'callStats'   => $callModel->getStatsForPerformer($performerId),
        ]);
    }

    public function toggleStatus(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $performerId = (int)$_SESSION['performer_id'];

        $this->db->prepare("
            UPDATE performers
            SET online_status = NOT online_status, last_seen_at = NOW()
            WHERE id = ?
        ")->execute([$performerId]);

        $stmt = $this->db->prepare("SELECT online_status FROM performers WHERE id = ?");
        $stmt->execute([$performerId]);
        $newStatus = (bool)$stmt->fetchColumn();

        $this->json(['online' => $newStatus]);
    }

    public function earnings(): void
    {
        $performerId = (int)$_SESSION['performer_id'];

        $stmt = $this->db->prepare("
            SELECT p.*, pp.amount, pp.status AS payout_status, pp.period_start, pp.period_end, pp.created_at AS payout_date
            FROM performers p
            LEFT JOIN performer_payouts pp ON pp.performer_id = p.id
            WHERE p.id = ?
            ORDER BY pp.created_at DESC
        ");
        $stmt->execute([$performerId]);
        $rows = $stmt->fetchAll();

        $performer = $rows[0] ?? null;
        $payouts   = array_filter($rows, fn($r) => $r['payout_date'] !== null);

        $this->view('performer-dash/earnings', [
            'title'     => 'My Earnings',
            'performer' => $performer,
            'payouts'   => $payouts,
        ]);
    }

    public function uploadPhoto(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $performerId = (int)$_SESSION['performer_id'];

        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
            $_SESSION['flash_error'] = 'Please select a photo to upload.';
            $this->redirect(BASE_PATH . '/performer-dash');
            return;
        }

        try {
            $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['photo'], $performerId);

            $stmt = $this->db->prepare("UPDATE performers SET profile_photo = ? WHERE id = ?");
            $stmt->execute([$paths['path'], $performerId]);

            $_SESSION['flash_success'] = 'Profile photo updated successfully.';
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
        }

        $this->redirect(BASE_PATH . '/performer-dash');
    }

    public function updateSettings(): void
    {
        CSRF::validate($_POST['csrf_token'] ?? '') or $this->abort(403);

        $performerId = (int)$_SESSION['performer_id'];

        $videoEnabled = isset($_POST['video_enabled']) ? 1 : 0;
        $videoMinCredits = max(0.0, (float)($_POST['video_min_credits'] ?? 15.00));
        $videoMinMinutes = max(0, (int)($_POST['video_min_minutes'] ?? 10));
        $videoRatePerMinute = max(0.0, (float)($_POST['video_rate_per_minute'] ?? 5.00));

        $stmt = $this->db->prepare("
            UPDATE performers 
            SET video_enabled = ?, 
                video_min_credits = ?, 
                video_min_minutes = ?, 
                video_rate_per_minute = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $videoEnabled, 
            $videoMinCredits, 
            $videoMinMinutes, 
            $videoRatePerMinute, 
            $performerId
        ]);

        $_SESSION['flash_success'] = 'Video settings updated successfully.';
        $this->redirect(BASE_PATH . '/performer-dash');
    }
}

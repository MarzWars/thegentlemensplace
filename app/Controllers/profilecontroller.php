<?php
// app/Controllers/ProfileController.php
namespace App\Controllers;

use App\Core\{Controller, Validator, CSRF};
use App\Models\User;
use App\Config\Database;

class ProfileController extends Controller
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function index(): void
    {
        $userId = (int)$_SESSION['user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            // Session references a deleted account — log out cleanly
            session_destroy();
            $this->redirect(BASE_PATH . '/login');
        }

        // Refresh session credits from DB (may have changed since login)
        $_SESSION['credits'] = $user['credit_balance'];

        // Recent transactions
        $txStmt = $this->db->prepare("
            SELECT t.*, cp.name AS package_name
            FROM transactions t
            LEFT JOIN credit_packages cp ON t.package_id = cp.id
            WHERE t.user_id = ?
            ORDER BY t.created_at DESC
            LIMIT 10
        ");
        $txStmt->execute([$userId]);
        $transactions = $txStmt->fetchAll();

        // Recent calls
        $callStmt = $this->db->prepare("
            SELECT c.*, p.display_name AS performer_name, p.slug AS performer_slug
            FROM calls c
            JOIN performers p ON c.performer_id = p.id
            WHERE c.user_id = ?
            ORDER BY c.created_at DESC
            LIMIT 10
        ");
        $callStmt->execute([$userId]);
        $calls = $callStmt->fetchAll();

        // Credit ledger summary
        $ledgerStmt = $this->db->prepare("
            SELECT type, SUM(amount) AS total
            FROM credit_ledger
            WHERE user_id = ?
            GROUP BY type
        ");
        $ledgerStmt->execute([$userId]);
        $ledger = [];
        foreach ($ledgerStmt->fetchAll() as $row) {
            $ledger[$row['type']] = $row['total'];
        }

        // Fetch linked performer record if any
        $perfStmt = $this->db->prepare("SELECT * FROM performers WHERE user_id = ? LIMIT 1");
        $perfStmt->execute([$userId]);
        $performer = $perfStmt->fetch();

        $this->view('account/index', [
            'title'        => 'My Account',
            'user'         => $user,
            'transactions' => $transactions,
            'calls'        => $calls,
            'ledger'       => $ledger,
            'performer'    => $performer ?: null,
        ]);
    }

    public function update(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403, 'Invalid request.');
        }

        $userId = (int)$_SESSION['user_id'];
        $userModel = new User();
        $user = $userModel->findById($userId);

        if (!$user) {
            $this->redirect(BASE_PATH . '/login');
        }

        $action = $_POST['action'] ?? '';

        // ── Change password ──────────────────────────────
        if ($action === 'change_password') {
            $current = $_POST['current_password'] ?? '';
            $new     = $_POST['new_password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            if (!password_verify($current, $user['password_hash'])) {
                $this->flashError('Current password is incorrect.');
                $this->redirect(BASE_PATH . '/account#security');
            }

            if (strlen($new) < 8) {
                $this->flashError('New password must be at least 8 characters.');
                $this->redirect(BASE_PATH . '/account#security');
            }

            if ($new !== $confirm) {
                $this->flashError('New passwords do not match.');
                $this->redirect(BASE_PATH . '/account#security');
            }

            $userModel->updatePassword($userId, password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]));
            $this->flashSuccess('Password updated successfully.');
            $this->redirect(BASE_PATH . '/account#security');
        }

        // ── Update profile details ────────────────────────
        if ($action === 'update_profile') {
            $v = new Validator();
            if (!$v->validate($_POST, ['phone' => 'max:20'])) {
                $this->flashError('Invalid phone number.');
                $this->redirect(BASE_PATH . '/account?tab=profile');
            }

            $phone = preg_replace('/[^+\d\s\-()]/', '', $_POST['phone'] ?? '');
            $userModel->updateProfile($userId, ['phone' => $phone ?: null]);

            // Refresh session
            $_SESSION['credits'] = $user['credit_balance'];

            $this->flashSuccess('Profile updated.');
            $this->redirect(BASE_PATH . '/account?tab=profile');
        }

        // ── Update performer profile details ──────────────
        if ($action === 'update_performer_profile') {
            // Fetch performer profile
            $stmt = $this->db->prepare("SELECT id FROM performers WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $performer = $stmt->fetch();

            if (!$performer) {
                $this->flashError('No performer profile linked to this account.');
                $this->redirect(BASE_PATH . '/account?tab=profile');
            }

            $performerId = (int)$performer['id'];

            $displayName   = trim($_POST['display_name'] ?? '');
            $age           = (int)($_POST['age'] ?? 0);
            $ratePerMinute = (float)($_POST['rate_per_minute'] ?? 1.00);
            $languages     = trim($_POST['languages'] ?? 'English');
            $category      = $_POST['category'] ?? 'chat';
            $bio           = trim($_POST['bio'] ?? '');

            if (!$displayName || $age <= 0) {
                $this->flashError('Display Name and Age are required.');
                $this->redirect(BASE_PATH . '/account?tab=profile');
            }

            $this->db->beginTransaction();
            try {
                // Update text fields
                $updateStmt = $this->db->prepare("
                    UPDATE performers
                    SET display_name = ?, age = ?, rate_per_minute = ?, languages = ?, category = ?, bio = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $updateStmt->execute([$displayName, $age, $ratePerMinute, $languages, $category, $bio, $performerId]);

                // Upload profile photo if present
                if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['profile_photo'], $performerId);
                    $upPhoto = $this->db->prepare("UPDATE performers SET profile_photo = ? WHERE id = ?");
                    $upPhoto->execute([$paths['path'], $performerId]);
                }

                // Upload cover photo if present
                if (isset($_FILES['cover_photo']) && $_FILES['cover_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
                    $paths = \App\Services\FileUpload::savePerformerPhoto($_FILES['cover_photo'], $performerId);
                    $upCover = $this->db->prepare("UPDATE performers SET cover_photo = ? WHERE id = ?");
                    $upCover->execute([$paths['path'], $performerId]);
                }

                // Upload short video if present
                if (isset($_FILES['short_video']) && $_FILES['short_video']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Get old video path to delete it
                    $oldVidStmt = $this->db->prepare("SELECT short_video FROM performers WHERE id = ?");
                    $oldVidStmt->execute([$performerId]);
                    $oldVideoPath = $oldVidStmt->fetchColumn();

                    $videoPath = \App\Services\FileUpload::savePerformerVideo($_FILES['short_video'], $performerId);
                    $upVideo = $this->db->prepare("UPDATE performers SET short_video = ? WHERE id = ?");
                    $upVideo->execute([$videoPath, $performerId]);

                    if ($oldVideoPath && file_exists(PUBLIC_PATH . '/' . $oldVideoPath)) {
                        @unlink(PUBLIC_PATH . '/' . $oldVideoPath);
                    }
                }

                // Upload voice sample if present
                if (isset($_FILES['voice_sample']) && $_FILES['voice_sample']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Get old voice path to delete it
                    $oldVoiceStmt = $this->db->prepare("SELECT voice_sample FROM performers WHERE id = ?");
                    $oldVoiceStmt->execute([$performerId]);
                    $oldVoicePath = $oldVoiceStmt->fetchColumn();

                    $voicePath = \App\Services\FileUpload::savePerformerVoice($_FILES['voice_sample'], $performerId);
                    $upVoice = $this->db->prepare("UPDATE performers SET voice_sample = ? WHERE id = ?");
                    $upVoice->execute([$voicePath, $performerId]);

                    if ($oldVoicePath && file_exists(PUBLIC_PATH . '/' . $oldVoicePath)) {
                        @unlink(PUBLIC_PATH . '/' . $oldVoicePath);
                    }
                }

                $this->db->commit();
                $this->flashSuccess('Performer profile updated successfully.');
            } catch (\Exception $e) {
                $this->db->rollBack();
                $this->flashError('Profile update failed: ' . $e->getMessage());
            }

            $this->redirect(BASE_PATH . '/account?tab=profile');
        }

        // ── Delete performer profile media ──────────────
        if ($action === 'delete_performer_media') {
            // Fetch performer profile
            $stmt = $this->db->prepare("SELECT id, profile_photo, cover_photo, short_video, voice_sample FROM performers WHERE user_id = ? LIMIT 1");
            $stmt->execute([$userId]);
            $performer = $stmt->fetch();

            if (!$performer) {
                $this->flashError('No performer profile linked to this account.');
                $this->redirect(BASE_PATH . '/account?tab=profile');
            }

            $performerId = (int)$performer['id'];
            $mediaType = $_POST['media_type'] ?? '';

            if (in_array($mediaType, ['profile_photo', 'cover_photo', 'short_video', 'voice_sample'])) {
                $oldPath = $performer[$mediaType];
                if ($oldPath) {
                    // Update database to NULL
                    $up = $this->db->prepare("UPDATE performers SET {$mediaType} = NULL, updated_at = NOW() WHERE id = ?");
                    $up->execute([$performerId]);

                    // Delete file from disk
                    if (file_exists(PUBLIC_PATH . '/' . $oldPath)) {
                        @unlink(PUBLIC_PATH . '/' . $oldPath);
                    }
                    
                    // If it is a profile photo, also delete the thumb_ thumbnail
                    if ($mediaType === 'profile_photo') {
                        $thumbPath = str_replace('uploads/performers/' . $performerId . '/', 'uploads/performers/' . $performerId . '/thumb_', $oldPath);
                        if (file_exists(PUBLIC_PATH . '/' . $thumbPath)) {
                            @unlink(PUBLIC_PATH . '/' . $thumbPath);
                        }
                    }
                    
                    $this->flashSuccess(ucwords(str_replace('_', ' ', $mediaType)) . ' deleted successfully.');
                } else {
                    $this->flashError('No file exists to delete.');
                }
            } else {
                $this->flashError('Invalid media type.');
            }

            $this->redirect(BASE_PATH . '/account?tab=profile');
        }

        $this->redirect(BASE_PATH . '/account');
    }
}

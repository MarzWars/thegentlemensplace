<?php
// app/Controllers/AuthController.php
namespace App\Controllers;

use App\Core\{Controller, Validator, CSRF, RateLimit};
use App\Models\User;
use App\Services\Mailer;

class AuthController extends Controller
{
    // ── Show forms ────────────────────────────────────────

    public function showRegister(): void
    {
        $this->view('auth/register', ['title' => 'Create Account']);
    }

    public function showLogin(): void
    {
        $this->view('auth/login', ['title' => 'Sign In']);
    }

    public function showForgot(): void
    {
        $this->view('auth/forgot', ['title' => 'Reset Password']);
    }

    public function showReset(string $token): void
    {
        $token = preg_replace('/[^a-f0-9]/', '', $token);
        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->flashError('This reset link is invalid or has expired.');
            $this->redirect(BASE_PATH . '/forgot-password');
        }

        $this->view('auth/reset', ['title' => 'Set New Password', 'token' => $token]);
    }

    // ── Register ──────────────────────────────────────────

    public function register(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403, 'Invalid request.');
        }

        if (!RateLimit::check('register', $_SERVER['REMOTE_ADDR'], 3, 3600)) {
            $this->flashError('Too many registrations from this IP. Please try again later.');
            $this->redirect(BASE_PATH . '/register');
        }

        $v = new Validator();
        $rules = [
            'username'  => 'required|min:3|max:50',
            'email'     => 'required|email|max:255',
            'password'  => 'required|min:8|max:128',
            'dob'       => 'required|age_18',
            'agree_toc' => 'required',
            'agree_age' => 'required',
        ];

        if (!$v->validate($_POST, $rules)) {
            $this->view('auth/register', [
                'title'  => 'Create Account',
                'errors' => $v->getErrors(),
            ]);
            return;
        }

        $email    = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES);

        $userModel = new User();

        if ($userModel->findByEmail($email)) {
            $this->view('auth/register', [
                'title'  => 'Create Account',
                'errors' => ['email' => ['This email address is already registered.']],
            ]);
            return;
        }

        if ($userModel->findByUsername($username)) {
            $this->view('auth/register', [
                'title'  => 'Create Account',
                'errors' => ['username' => ['This username is already taken.']],
            ]);
            return;
        }

        $userId = $userModel->create([
            'uuid'               => User::generateUuid(),
            'username'           => $username,
            'email'              => $email,
            'password_hash'      => password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            'date_of_birth'      => $_POST['dob'],
            'status'             => 'active',
            'age_verified'       => 1,
            'age_verified_at'    => date('Y-m-d H:i:s'),
            'age_verify_method'  => 'dob_declaration',
        ]);

        // Auto-verify email
        $userModel->verifyEmail($userId);

        $user = $userModel->findById($userId);
        if ($user) {
            session_regenerate_id(true);

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_uuid'] = $user['uuid'];
            $_SESSION['username']  = $user['username'];
            $_SESSION['credits']   = $user['credit_balance'];

            $userModel->updateLastLogin($user['id'], $_SERVER['REMOTE_ADDR']);

            $redirect = $_SESSION['login_redirect'] ?? (BASE_PATH . '/account');
            unset($_SESSION['login_redirect']);
            $this->redirect($redirect);
        } else {
            $this->redirect(BASE_PATH . '/login');
        }
    }

    // ── Login ─────────────────────────────────────────────

    public function login(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403, 'Invalid request.');
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        $email    = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $user      = $userModel->findByEmail($email);

        // Always run password_verify to prevent timing attacks
        $valid = $user && password_verify($password, $user['password_hash']);

        if (!$valid) {
            $this->view('auth/login', [
                'title' => 'Sign In',
                'error' => 'Invalid email or password.',
            ]);
            return;
        }

        if ($user['status'] === 'banned' || $user['status'] === 'suspended') {
            $this->view('auth/login', [
                'title' => 'Sign In',
                'error' => 'Your account has been suspended. Contact support.',
            ]);
            return;
        }

        if (!$user['email_verified']) {
            $this->view('auth/login', [
                'title' => 'Sign In',
                'error' => 'Please verify your email address before signing in.',
            ]);
            return;
        }

        // Upgrade hash cost if needed
        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            $userModel->updatePassword($user['id'], password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]));
        }

        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_uuid'] = $user['uuid'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['credits']   = $user['credit_balance'];

        // Detect if this user is a performer
        $db = \App\Config\Database::getInstance();
        $stmt = $db->prepare("SELECT id FROM performers WHERE user_id = ? LIMIT 1");
        $stmt->execute([$user['id']]);
        $performer = $stmt->fetch();
        if ($performer) {
            $_SESSION['performer_id'] = (int)$performer['id'];
        }

        $userModel->updateLastLogin($user['id'], $ip);

        // If they are a performer, send them to the performer dash by default
        $defaultRedirect = $performer ? (BASE_PATH . '/performer-dash') : (BASE_PATH . '/account');
        $redirect = $_SESSION['login_redirect'] ?? $defaultRedirect;
        unset($_SESSION['login_redirect']);
        $this->redirect($redirect);
    }

    // ── Logout ────────────────────────────────────────────

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
        $this->redirect(BASE_PATH . '/');
    }

    // ── Email verification ────────────────────────────────

    public function verifyEmail(string $token): void
    {
        $token = preg_replace('/[^a-f0-9]/', '', $token);
        $userModel = new User();
        $user = $userModel->findByVerifyToken($token);

        if (!$user) {
            $this->flashError('This verification link is invalid or has expired.');
            $this->redirect(BASE_PATH . '/login');
        }

        $userModel->verifyEmail($user['id']);
        $this->redirect(BASE_PATH . '/login?msg=verified');
    }

    // ── Forgot password ───────────────────────────────────

    public function sendReset(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);

        // Always show success to prevent email enumeration
        $successRedirect = BASE_PATH . '/login?msg=reset_sent';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->redirect($successRedirect);
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $userModel->setResetToken($user['id'], $token);
            try {
                Mailer::sendPasswordReset($email, $user['username'], $token);
            } catch (\Exception $e) {
                error_log('[Auth] Reset email failed: ' . $e->getMessage());
            }
        }

        $this->redirect($successRedirect);
    }

    // ── Reset password ────────────────────────────────────

    public function resetPassword(): void
    {
        if (!CSRF::validate($_POST['csrf_token'] ?? '')) {
            $this->abort(403);
        }

        $token    = preg_replace('/[^a-f0-9]/', '', $_POST['token'] ?? '');
        $password = $_POST['password'] ?? '';

        if (strlen($password) < 8) {
            $this->view('auth/reset', [
                'title' => 'Set New Password',
                'token' => $token,
                'error' => 'Password must be at least 8 characters.',
            ]);
            return;
        }

        $userModel = new User();
        $user = $userModel->findByResetToken($token);

        if (!$user) {
            $this->flashError('This reset link is invalid or has expired.');
            $this->redirect(BASE_PATH . '/forgot-password');
        }

        $userModel->updatePassword($user['id'], password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]));
        $userModel->clearResetToken($user['id']);

        $this->redirect(BASE_PATH . '/login?msg=password_reset');
    }
}

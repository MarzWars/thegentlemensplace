<?php
// app/Core/Middleware.php
namespace App\Core;

class Middleware
{
    public function handle(array $middleware): bool
    {
        foreach ($middleware as $mw) {
            if (!$this->$mw()) return false;
        }
        return true;
    }

    private function auth(): bool
    {
        if (empty($_SESSION['user_id'])) {
            $_SESSION['login_redirect'] = BASE_PATH . $_SERVER['REQUEST_URI'];
            header('Location: ' . BASE_PATH . '/login');
            return false;
        }
        return true;
    }

    private function guest(): bool
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_PATH . '/account');
            return false;
        }
        return true;
    }

    private function age_gate(): bool
    {
        if (empty($_COOKIE['age_confirmed'])) {
            // Will show age gate overlay on the page via JS
            // Store intended destination in session
            $_SESSION['age_gate_redirect'] = $_SERVER['REQUEST_URI'];
        }
        return true; // always returns true — gate is shown as overlay, not redirect
    }

    private function age_verified(): bool
    {
        if (empty($_SESSION['user_id'])) { header('Location: ' . BASE_PATH . '/login'); return false; }
        $db   = \App\Config\Database::getInstance();
        $stmt = $db->prepare("SELECT age_verified FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        if (!$user || !$user['age_verified']) {
            header('Location: ' . BASE_PATH . '/account?msg=age_required');
            return false;
        }
        return true;
    }

    private function admin(): bool
    {
        if (empty($_SESSION['admin_id'])) {
            header('Location: ' . BASE_PATH . '/admin/login');
            return false;
        }
        return true;
    }

    private function performer(): bool
    {
        if (empty($_SESSION['performer_id'])) {
            header('Location: ' . BASE_PATH . '/login');
            return false;
        }
        return true;
    }
}
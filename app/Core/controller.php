<?php
namespace App\Core;

class Controller
{
    protected function view(string $template, array $data = []): void
    {
        $view = new View();
        $view->render($template, $data);
    }

    protected function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        if ($message) echo htmlspecialchars($message);
        exit;
    }

    protected function redirect(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }

    protected function flashError(string $msg): void
    {
        $_SESSION['flash_error'] = $msg;
    }

    protected function flashSuccess(string $msg): void
    {
        $_SESSION['flash_success'] = $msg;
    }
}

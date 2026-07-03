<?php
namespace App\Core;

class View
{
    private string $viewsPath;

    public function __construct()
    {
        $this->viewsPath = APP_ROOT . '/Views';
    }

    public function render(string $template, array $data = []): void
    {
        $file = $this->viewsPath . '/' . str_replace('.', '/', $template) . '.php';

        if (!file_exists($file)) {
            http_response_code(500);
            echo 'View not found: ' . htmlspecialchars($template);
            return;
        }

        // Extract data into local scope
        extract($data, EXTR_SKIP);

        // Capture view content
        ob_start();
        require $file;
        $content = ob_get_clean();

        // Render inside layout if a layout variable is set
        $layout = $data['layout'] ?? 'main';

        // Allow views to opt out of a layout entirely
        if ($layout === false || $layout === 'none') {
            echo $content;
            return;
        }

        $layoutFile = $this->viewsPath . '/layouts/' . $layout . '.php';

        if (file_exists($layoutFile)) {
            require $layoutFile;
        } else {
            echo $content;
        }
    }
}

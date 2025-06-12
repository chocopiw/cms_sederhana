<?php
namespace Core;

class Controller
{
    protected function view($view, $data = [])
    {
        // Extract data to variables
        extract($data);
        
        $viewFile = APP_PATH . '/views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            ob_start();
            include $viewFile;
            $content = ob_get_clean();
            echo $content;
        } else {
            throw new \Exception("View {$view} not found");
        }
    }

    protected function redirect($url)
    {
        header("Location: {$url}");
        exit();
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    protected function requireAuth()
    {
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
    }

    protected function getPostData()
    {
        return $_POST;
    }

    protected function getQueryData()
    {
        return $_GET;
    }
} 
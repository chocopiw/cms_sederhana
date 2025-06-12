<?php
class HomeController {
    public function index() {
        require_once __DIR__ . '/../models/HomeModel.php';
        $model = new HomeModel();
        $data = $model->getWelcomeMessage();
        include __DIR__ . '/../views/home.php';
    }
} 
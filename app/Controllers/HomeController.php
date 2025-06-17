<?php
class HomeController extends Controller
{
    public function index()
    {
        $postModel = $this->model('PostModel');
        $recentPosts = $postModel->getRecentPosts(3);

        $this->view('home', [
            'recentPosts' => $recentPosts,
            'siteTitle' => 'CMS Sederhana', // Contoh data untuk judul situs
            'username' => 'vivi' // Contoh data untuk nama user
        ]);
    }
} 
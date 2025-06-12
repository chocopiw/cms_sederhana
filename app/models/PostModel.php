<?php

class PostModel extends Model
{
    public function getRecentPosts($limit = 3)
    {
        // Menggunakan $this->db dari kelas Model dasar
        return $this->db->fetchAll(
            "SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC LIMIT ?",
            [$limit]
        );
    }
} 
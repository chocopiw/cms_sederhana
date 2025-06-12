<?php
class Model {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
    // Kosongkan atau isi sesuai kebutuhan
} 
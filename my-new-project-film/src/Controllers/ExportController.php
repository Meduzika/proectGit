<?php
namespace Controllers;

use Exports\ExcelExport;
use Exports\WordExport;

class ExportController {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Необходимо войти в систему'];
            header('Location: ?page=login');
            exit;
        }
        
        $stmt = $this->pdo->query("SELECT id, title FROM movies ORDER BY title");
        $movies = $stmt->fetchAll();
        
        require __DIR__ . '/../Views/export/index.php';
    }
    
    public function excelTop10() {
        $this->checkAuth();
        $export = new ExcelExport($this->pdo);
        $export->exportTop10();
    }
    
    public function excelAllReviews() {
        $this->checkAuth();
        $export = new ExcelExport($this->pdo);
        $export->exportAllReviews();
    }
    
    public function excelUsersStats() {
        $this->checkAuth();
        $export = new ExcelExport($this->pdo);
        $export->exportUsersStats();
    }
    
    public function wordTop10() {
        $this->checkAuth();
        $export = new WordExport($this->pdo);
        $export->exportTop10();
    }
    
    public function wordMovieReviews() {
        $this->checkAuth();
        $movieId = (int)($_GET['movie_id'] ?? 0);
        if ($movieId > 0) {
            $export = new WordExport($this->pdo);
            $export->exportMovieReviews($movieId);
        }
        header('Location: ?page=export');
    }
    
    private function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['errors'] = ['Необходимо войти в систему'];
            header('Location: ?page=login');
            exit;
        }
    }
}
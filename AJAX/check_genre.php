<?php
 require_once '../classes/database.php';

header('Content-Type: application/json'); 

if (isset($_POST['genre_name'])) {
    $genreName = $_POST['genre_name']; 
    $con = new database();

    $db = $con->opencon();
    if (!$db) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $query = $db->prepare("SELECT genre_name FROM genres WHERE genre_name = ?");
    $query->execute([$genreName]);
    $existingUser = $query->fetch();

    if ($existingUser) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }
}

?>
 
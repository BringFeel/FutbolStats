<?php
require_once __DIR__ . '/../Class/Match.php';

$matchGame = new MatchGame();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['data'])) {
        $action = $_POST['action'];
        $data = $_POST['data'];
        $matchGame->addData($action, $data);
    }
}

?>
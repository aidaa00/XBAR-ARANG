<?php
require_once __DIR__ . '/config/database.php';

header('Content-Type: application/json');

$event = trim($_POST['event'] ?? $_GET['event'] ?? '');
if (!empty($event)) {
    $cleanEvent = substr(preg_replace('/[^a-zA-Z0-9_\-\.\/ ]/', '', $event), 0, 100);
    trackVisitor('click:' . $cleanEvent);
    echo json_encode(['status' => 'success', 'event' => $cleanEvent]);
    exit;
}

echo json_encode(['status' => 'ignored']);

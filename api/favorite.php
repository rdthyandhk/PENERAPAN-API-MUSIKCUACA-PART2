<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'list') {
    $rows = $pdo->query("SELECT * FROM favorite_music ORDER BY created_at DESC")->fetchAll();
    echo json_encode(['success' => true, 'data' => $rows]);
    exit;
}

if ($action === 'toggle') {
    $id = (int)($_POST['deezer_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $album = trim($_POST['album'] ?? '');
    $cover = trim($_POST['cover'] ?? '');
    $preview = trim($_POST['preview'] ?? '');
    $link = trim($_POST['link'] ?? '');

    if (!$id || !$title || !$artist) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Data lagu tidak lengkap.']);
        exit;
    }

    $check = $pdo->prepare("SELECT id FROM favorite_music WHERE deezer_id = ?");
    $check->execute([$id]);
    $existing = $check->fetch();

    if ($existing) {
        $stmt = $pdo->prepare("DELETE FROM favorite_music WHERE deezer_id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true, 'favorited' => false, 'message' => 'Dihapus dari favorit.']);
    } else {
        $stmt = $pdo->prepare("INSERT INTO favorite_music (deezer_id,title,artist,album,cover,preview,deezer_link) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$id,$title,$artist,$album,$cover,$preview,$link]);
        echo json_encode(['success' => true, 'favorited' => true, 'message' => 'Ditambahkan ke favorit.']);
    }
    exit;
}

if ($action === 'remove') {
    $id = (int)($_POST['deezer_id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM favorite_music WHERE deezer_id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Action tidak valid.']);
?>

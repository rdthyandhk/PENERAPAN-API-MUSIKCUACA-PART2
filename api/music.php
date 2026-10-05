<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');

$query = trim($_GET['q'] ?? '');
if ($query === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Query musik kosong.']);
    exit;
}

$encoded = urlencode($query);
$url = "https://itunes.apple.com/search?term={$encoded}&entity=song&limit=12&country=id";

$context = stream_context_create([
    'http' => [
        'timeout' => 10,
        'header' => "User-Agent: DTZY-Profile/1.0\r\nAccept: application/json\r\n"
    ]
]);

$response = @file_get_contents($url, false, $context);
if ($response === false) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'iTunes Search API tidak dapat diakses.']);
    exit;
}

$data = json_decode($response, true);
if (!isset($data['results']) || !is_array($data['results'])) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Respons iTunes Search API tidak valid.']);
    exit;
}

$tracks = array_values(array_map(static function (array $track): array {
    $artwork = $track['artworkUrl100'] ?? '';
    if ($artwork !== '') {
        $artwork = preg_replace('/\/\d+x\d+bb\./', '/600x600bb.', $artwork) ?: $artwork;
    }

    return [
        'id' => $track['trackId'] ?? 0,
        'title' => $track['trackName'] ?? 'Unknown',
        'artist' => ['name' => $track['artistName'] ?? 'Unknown'],
        'album' => [
            'title' => $track['collectionName'] ?? '',
            'cover_medium' => $artwork,
            'cover' => $artwork
        ],
        'preview' => $track['previewUrl'] ?? '',
        'link' => $track['trackViewUrl'] ?? ''
    ];
}, array_filter($data['results'], static function ($track): bool {
    return is_array($track) && !empty($track['trackId']);
})));

$stmt = $pdo->prepare("INSERT INTO search_history (type, query_text) VALUES ('music', ?)");
$stmt->execute([$query]);

echo json_encode([
    'success' => true,
    'total' => $data['resultCount'] ?? count($tracks),
    'data' => $tracks
]);
?>

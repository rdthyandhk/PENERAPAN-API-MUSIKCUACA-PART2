<?php
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/json; charset=utf-8');

$city = trim($_GET['city'] ?? '');
if ($city === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nama kota kosong.']);
    exit;
}

$geoUrl = 'https://geocoding-api.open-meteo.com/v1/search?name=' . urlencode($city) . '&count=1&language=id&format=json';
$geo = @file_get_contents($geoUrl);

if ($geo === false) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Geocoding API tidak dapat diakses.']);
    exit;
}

$geoData = json_decode($geo, true);
if (empty($geoData['results'][0])) {
    echo json_encode(['success' => false, 'message' => 'Kota tidak ditemukan.']);
    exit;
}

$place = $geoData['results'][0];
$lat = $place['latitude'];
$lon = $place['longitude'];

$url = "https://api.open-meteo.com/v1/forecast?latitude={$lat}&longitude={$lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&timezone=auto";
$weather = @file_get_contents($url);

if ($weather === false) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Weather API tidak dapat diakses.']);
    exit;
}

$weatherData = json_decode($weather, true);
$current = $weatherData['current'] ?? null;

if (!$current) {
    echo json_encode(['success' => false, 'message' => 'Data cuaca tidak tersedia.']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO search_history (type, query_text) VALUES ('weather', ?)");
$stmt->execute([$city]);

$code = (int)$current['weather_code'];
$description = match (true) {
    $code === 0 => 'Cerah',
    in_array($code, [1,2,3]) => 'Berawan',
    in_array($code, [45,48]) => 'Berkabut',
    in_array($code, [51,53,55,56,57]) => 'Gerimis',
    in_array($code, [61,63,65,66,67,80,81,82]) => 'Hujan',
    in_array($code, [71,73,75,77,85,86]) => 'Salju',
    in_array($code, [95,96,99]) => 'Badai',
    default => 'Tidak diketahui'
};

echo json_encode([
    'success' => true,
    'location' => [
        'name' => $place['name'],
        'country' => $place['country'] ?? '',
        'latitude' => $lat,
        'longitude' => $lon,
        'timezone' => $weatherData['timezone'] ?? ''
    ],
    'current' => [
        'temperature' => $current['temperature_2m'],
        'humidity' => $current['relative_humidity_2m'],
        'apparent' => $current['apparent_temperature'],
        'wind' => $current['wind_speed_10m'],
        'description' => $description,
        'weather_code' => $code,
        'time' => $current['time']
    ]
]);
?>

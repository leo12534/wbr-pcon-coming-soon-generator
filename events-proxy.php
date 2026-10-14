<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$endpoint = basename($path);

if ($endpoint === 'events-proxy.php' && !isset($_GET['url'])) {
    echo json_encode(['status' => 'ok']);
    exit;
}

$target = isset($_GET['url']) ? trim($_GET['url']) : '';

if (!$target || !preg_match('/^https?:\/\//', $target)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing or invalid url parameter']);
    exit;
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL            => $target,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 10,
    CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; PCon-Generator/2.0)',
    CURLOPT_HTTPHEADER     => ['Accept: text/html'],
]);
$html = curl_exec($ch);
$err  = curl_error($ch);
curl_close($ch);

if ($err || $html === false) {
    http_response_code(500);
    echo json_encode(['error' => $err ?: 'Failed to fetch URL']);
    exit;
}

$date  = '';
$venue = '';
$colors = [];

if (preg_match('/<p[^>]*class="[^"]*\bdata\b[^"]*"[^>]*>(.*?)<\/p>/si', $html, $m)) {
    $date = preg_replace('/<!--.*?-->/s', '', $m[1]);
    $date = strip_tags($date);
    $date = preg_replace('/\s+/', ' ', $date);
    $date = trim($date);
    $date = preg_replace('/\s*[-–]\s*/', ' — ', $date);
}

if (preg_match('/<p[^>]*class="[^"]*\bvenue\b[^"]*"[^>]*>(.*?)<\/p>/si', $html, $m)) {
    $venue = strip_tags($m[1]);
    $venue = preg_replace('/\s+/', ' ', $venue);
    $venue = trim($venue);
}

preg_match_all('/<style[\s\S]*?>([\s\S]*?)<\/style>/i', $html, $styleMatches);
$styleText = implode("\n", $styleMatches[1] ?? []);

$colorProps = [
    'bg' => '--event-header-color',
    'c1' => '--event-primary-color',
    'c2' => '--event-secondary-color',
    'c3' => '--event-tertiary-color',
];

foreach ($colorProps as $key => $prop) {
    if (preg_match('/' . preg_quote($prop, '/') . '\s*:\s*([^;\n}]+)/', $styleText, $m)) {
        $colors[$key] = trim($m[1]);
    }
}

echo json_encode([
    'date'   => $date,
    'venue'  => $venue,
    'colors' => $colors,
]);

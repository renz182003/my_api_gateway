<?php
require_once "config.php";

$headers = getallheaders();
$apiKey = $headers["X-API-Key"] ?? null;
$requestPath = $_GET["request_path"] ?? "";
$clientIp = $_SERVER["REMOTE_ADDR"];
$statusCode = 200;

if (!$apiKey || !array_key_exists($apiKey, $valid_api_keys)) {
    http_response_code(401);
    echo json_encode(["error" => "Invalid or missing API Key"]);
    $statusCode = 401;
    logRequest($clientIp, $apiKey, $requestPath, $statusCode);
    exit;
}

// Rate limiting
$rateFile = __DIR__ . "/ratelimit_data/{$apiKey}.json";
$currentTime = time();
$limit = 10;
$window = 60;
$data = ["timestamp" => $currentTime, "count" => 0];

if (file_exists($rateFile)) {
    $data = json_decode(file_get_contents($rateFile), true);
    if ($currentTime - $data["timestamp"] > $window) {
        $data = ["timestamp" => $currentTime, "count" => 1];
    } else {
        if ($data["count"] >= $limit) {
            http_response_code(429);
            echo json_encode(["error" => "Rate limit exceeded"]);
            $statusCode = 429;
            logRequest($clientIp, $apiKey, $requestPath, $statusCode);
            exit;
        }
        $data["count"]++;
    }
} else {
    $data = ["timestamp" => $currentTime, "count" => 1];
}

file_put_contents($rateFile, json_encode($data));

// Routing
switch ($requestPath) {
    case "users":
        include "services/service_users.php";
        break;
    case "products":
        include "services/service_products.php";
        break;
    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint not found"]);
        $statusCode = 404;
        break;
}

logRequest($clientIp, $apiKey, $requestPath, $statusCode);

function logRequest($ip, $key, $path, $code) {
    $log = "[" . date("Y-m-d H:i:s") . "] - IP: $ip - API Key: " . ($key ?: "None") . " - Path: $path - Status: $code
";
    file_put_contents(__DIR__ . "/logs/gateway.log", $log, FILE_APPEND);
}
?>
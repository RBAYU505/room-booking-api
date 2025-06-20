<?php
require_once '../config/database.php';
require_once '../routes/users.php';
require_once '../routes/rooms.php';
require_once '../routes/bookings.php';

header("Content-Type: application/json");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($path, '/users') === 0) {
    handleUsers($path, $method);
} elseif (strpos($path, '/rooms') === 0) {
    handleRooms($path, $method);
} elseif (strpos($path, '/bookings') === 0) {
    handleBookings($path, $method);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint tidak ditemukan."]);
}

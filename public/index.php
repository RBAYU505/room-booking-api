<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';
require_once '../routes/users.php';
require_once '../routes/rooms.php';
require_once '../routes/bookings.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (strpos($path, '/users') === 0) { //POST
    handleUsers($path, $method);
} elseif (strpos($path, '/rooms') === 0) { //GET
    //Get All Rooms             -> [GET]  ../rooms
    //Get Rooms By Id           -> [GET]  ../rooms?id=idroom
    handleRooms($path, $method);
} elseif (strpos($path, '/bookings') === 0) { //GET & POST
    //Create Booking            -> [POST] ../bookings
    //Edit Booking              -> [POST] ../bookings?m=edit&p=idbooking
    //Delete Booking            -> [POST] ../bookings?m=delete&p=idbooking
    //Get All Bookings          -> [GET]  ../bookings?m=get
    //Get Bookings By Userid    -> [GET]  ../bookings?m=get&p=userid
    handleBookings($path, $method);
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint tidak ditemukan."]);
}

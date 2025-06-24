<?php
function handleBookings($path, $method)
{
    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    $parts = explode('?', $path);
    $endpoint = $parts[0];
    $query = $parts[1] ?? '';

    $queryParams = [];
    if (!empty($query)) {
        parse_str($query, $queryParams);
    }
    $mtd = $queryParams['m'] ?? null;
    $prm = $queryParams['p'] ?? null;

    // print_r($path);

    if ($endpoint == "/bookings" && $mtd === null && $prm === null && $method == "POST") { //CREATE BOOKING
        $user_id = $data['user_id'];
        $room_id = $data['room_id'];
        $activity = $data['activity_name'];
        $start = $data['start_time'];
        $end = $data['end_time'];

        $conflict_query = "SELECT * FROM bookings WHERE room_id = $1 AND start_time < $2 AND end_time > $3";
        $conflict_res = pg_query_params($db, $conflict_query, [$room_id, $end, $start]);

        if (pg_num_rows($conflict_res) > 0) {
            http_response_code(409);
            echo json_encode(["rc" => "409", "message" => "Ruangan sudah dibooking di waktu tersebut."]);
            return;
        }

        $insert = "INSERT INTO bookings (user_id, room_id, activity_name, start_time, end_time) VALUES ($1, $2, $3, $4, $5)";
        pg_query_params($db, $insert, [$user_id, $room_id, $activity, $start, $end]);

        echo json_encode(["rc" => "00", "message" => "Booking berhasil disimpan."]);
    } elseif ($endpoint == "/bookings" && $mtd == 'edit' && $prm !== null && $method == "POST") { //EDIT BOOKING
        $id = $prm; //id booking
        $room_id = $data['room_id'];
        $activity = $data['activity_name'];
        $start = $data['start_time'];
        $end = $data['end_time'];

        $conflict_query = "SELECT * FROM bookings WHERE room_id = $1 AND start_time < $2 AND end_time > $3 AND booking_id != $4";
        $conflict_res = pg_query_params($db, $conflict_query, [$room_id, $end, $start, $id]);

        if (pg_num_rows($conflict_res) > 0) {
            http_response_code(409);
            echo json_encode(["rc" => "409", "message" => "Jadwal bentrok dengan booking lain."]);
            return;
        }

        $update = "UPDATE bookings SET room_id=$1, activity_name=$2, start_time=$3, end_time=$4 WHERE booking_id=$7";
        pg_query_params($db, $update, [$room_id, $activity, $start, $end, $id]);

        echo json_encode(["rc" => "00", "message" => "Booking berhasil diperbarui."]);
    } elseif ($endpoint == "/bookings" && $mtd == 'delete' && $prm !== null && $method == "POST") { //DELETE BOOKING
        $id = $prm; //id booking

        $check = pg_query_params($db, "SELECT * FROM bookings WHERE booking_id = $1", [$id]);
        if (pg_num_rows($check) == 0) {
            http_response_code(404);
            echo json_encode(["rc" => "404", "message" => "Booking tidak ditemukan."]);
            return;
        }

        pg_query_params($db, "DELETE FROM bookings WHERE booking_id = $1", [$id]);
        echo json_encode(["rc" => "00", "message" => "Booking berhasil dibatalkan."]);
    } elseif ($endpoint == "/bookings" && $mtd == 'iget' && $prm !== null && $method == "GET") { //GET BOOKING BY IDBOOKING
        $uid = $prm;
        $res = pg_query_params($db, "SELECT * FROM bookings WHERE id = $1 ORDER BY start_time", [$uid]);
        $bookings = [];
        while ($b = pg_fetch_assoc($res)) {
            $bookings[] = $b;
        }
        echo json_encode($bookings);
    } elseif ($endpoint == "/bookings" && $mtd == 'uget' && $prm !== null && $method == "GET") { //GET BOOKING BY USERS
        $uid = $prm;
        $res = pg_query_params($db, "SELECT r.name as ruangan, b.* FROM bookings b JOIN rooms r ON r.id=b.room_id WHERE b.user_id = $1 ORDER BY b.start_time", [$uid]);
        $bookings = [];
        while ($b = pg_fetch_assoc($res)) {
            $bookings[] = $b;
        }
        echo json_encode($bookings);
    } elseif ($endpoint == "/bookings" && $mtd == 'rget' && $prm !== null && $method == "GET") { //GET BOOKING BY IDROOM
        $uid = $prm;
        $res = pg_query_params($db, "SELECT * FROM bookings WHERE room_id = $1 ORDER BY start_time", [$uid]);
        $bookings = [];
        while ($b = pg_fetch_assoc($res)) {
            $bookings[] = $b;
        }
        echo json_encode($bookings);
    } elseif ($endpoint == "/bookings" && $mtd == 'get' && $prm === null && $method == "GET") { //GET ALL BOOKING
        $res = pg_query($db, "SELECT r.name as ruangan, b.* FROM bookings b JOIN rooms r ON r.id=b.room_id ORDER BY b.start_time");
        $bookings = [];
        while ($b = pg_fetch_assoc($res)) {
            $bookings[] = $b;
        }
        echo json_encode($bookings);
    }
}

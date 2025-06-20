<?php
function handleBookings($path, $method)
{
    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    // create booking baru
    if ($path == "/bookings" && $method == "POST") {
        $user_id = $data['user_id'];
        $room_id = $data['room_id'];
        $activity = $data['activity_name'];
        $start = $data['start_time'];
        $end = $data['end_time'];
        $count = $data['participant_count'];
        $contact = $data['contact_info'];

        $conflict_query = "SELECT * FROM room_bookings WHERE room_id = $1 AND start_time < $2 AND end_time > $3";
        $conflict_res = pg_query_params($db, $conflict_query, [$room_id, $end, $start]);

        if (pg_num_rows($conflict_res) > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Ruangan sudah dibooking di waktu tersebut."]);
            return;
        }

        $insert = "INSERT INTO room_bookings (user_id, room_id, activity_name, start_time, end_time, participant_count, contact_info) VALUES ($1, $2, $3, $4, $5, $6, $7)";
        pg_query_params($db, $insert, [$user_id, $room_id, $activity, $start, $end, $count, $contact]);

        echo json_encode(["message" => "Booking berhasil disimpan."]);
    }

    // get booking berdasarkan user
    elseif (preg_match("/\/bookings\/user\/(\d+)/", $path, $m) && $method == "GET") {
        $uid = $m[1];
        $res = pg_query_params($db, "SELECT * FROM room_bookings WHERE user_id = $1 ORDER BY start_time", [$uid]);
        $bookings = [];
        while ($b = pg_fetch_assoc($res)) {
            $bookings[] = $b;
        }
        echo json_encode($bookings);
    }

    // edit booking
    elseif (preg_match("/\/bookings\/(\d+)/", $path, $m) && $method == "PUT") {
        $id = $m[1];
        $room_id = $data['room_id'];
        $activity = $data['activity_name'];
        $start = $data['start_time'];
        $end = $data['end_time'];
        $count = $data['participant_count'];
        $contact = $data['contact_info'];

        $conflict_query = "SELECT * FROM room_bookings WHERE room_id = $1 AND start_time < $2 AND end_time > $3 AND booking_id != $4";
        $conflict_res = pg_query_params($db, $conflict_query, [$room_id, $end, $start, $id]);

        if (pg_num_rows($conflict_res) > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Jadwal bentrok dengan booking lain."]);
            return;
        }

        $update = "UPDATE room_bookings SET room_id=$1, activity_name=$2, start_time=$3, end_time=$4, participant_count=$5, contact_info=$6 WHERE booking_id=$7";
        pg_query_params($db, $update, [$room_id, $activity, $start, $end, $count, $contact, $id]);

        echo json_encode(["message" => "Booking berhasil diperbarui."]);
    }

    // hapus booking
    elseif (preg_match("/\/bookings\/(\d+)/", $path, $m) && $method == "DELETE") {
        $id = $m[1];

        $check = pg_query_params($db, "SELECT * FROM room_bookings WHERE booking_id = $1", [$id]);
        if (pg_num_rows($check) == 0) {
            http_response_code(404);
            echo json_encode(["error" => "Booking tidak ditemukan."]);
            return;
        }

        pg_query_params($db, "DELETE FROM room_bookings WHERE booking_id = $1", [$id]);
        echo json_encode(["message" => "Booking berhasil dibatalkan."]);
    }
}

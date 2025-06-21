<?php
function handleRooms($path, $method)
{
    header('Content-Type: application/json');
    echo json_encode(["status" => "success", "data" => ["room1", "room2"]]);

    $db = getDB();

    if ($path == "/rooms" && $method == "GET") {
        $res = pg_query($db, "SELECT * FROM rooms WHERE is_active = TRUE");
        $rooms = [];
        while ($row = pg_fetch_assoc($res)) {
            $rooms[] = $row;
        }
        echo json_encode($rooms);
    }
}

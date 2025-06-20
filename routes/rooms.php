<?php
function handleRooms($path, $method)
{
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

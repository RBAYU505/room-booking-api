<?php
function handleRooms($path, $method)
{
    $db = getDB();

    $parts = explode('?', $path);
    $endpoint = $parts[0];
    $query = $parts[1] ?? '';

    $queryParams = [];
    if (!empty($query)) {
        parse_str($query, $queryParams);
    }
    $idroom = $queryParams['id'] ?? null;

    if ($endpoint == "/rooms" && $idroom !== null && $method == "GET") { //GET AKTIF ROMM BY ID
        $res = pg_query_params($db, "SELECT * FROM rooms WHERE is_active = TRUE AND id = $1", [$idroom]);
        $rooms = [];
        while ($row = pg_fetch_assoc($res)) {
            $rooms[] = $row;
        }
        echo json_encode($rooms);
    } elseif ($endpoint == "/rooms" && $method == "GET") { //GET ALL AKTIF ROOMS
        $res = pg_query($db, "SELECT * FROM rooms WHERE is_active = TRUE");
        $rooms = [];
        while ($row = pg_fetch_assoc($res)) {
            $rooms[] = $row;
        }
        echo json_encode($rooms);
    }
}

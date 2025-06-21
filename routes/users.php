<?php
function handleUsers($path, $method)
{
    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    if ($path == "/users/login" && $method == "POST") {
        $email = $data['email'];
        $password = $data['password'];

        $res = pg_query_params($db, "SELECT * FROM users WHERE email = $1", [$email]);
        $user = pg_fetch_assoc($res);

        if ($user && password_verify($password, $user['password'])) {
            echo json_encode(["message" => "Login berhasil.", "user_id" => $user['user_id']]);
        } else {
            http_response_code(401);
            echo json_encode(["error" => "Email atau password salah."]);
        }
    }
}

<?php
function handleUsers($path, $method)
{
    $db = getDB();
    $data = json_decode(file_get_contents("php://input"), true);

    if ($path == "/users/register" && $method == "POST") {
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);

        $check = pg_query_params($db, "SELECT * FROM users WHERE email = $1", [$email]);
        if (pg_num_rows($check) > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Email sudah digunakan."]);
            return;
        }

        pg_query_params($db, "INSERT INTO users (name, email, password, role) VALUES ($1, $2, $3, 'user')", [$name, $email, $password]);
        echo json_encode(["message" => "Registrasi berhasil."]);
    } elseif ($path == "/users/login" && $method == "POST") {
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

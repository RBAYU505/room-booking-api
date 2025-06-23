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

        if ($user && ($password === $user['password'])) {
            echo json_encode(["rc" => "00", "message" => "Login berhasil.", "email" => $user['email'], "userid" => $user["id"]]);
        } else {
            http_response_code(401);
            echo json_encode(["rc" => "99", "message" => "Email atau password salah.", "email" => $email, "userid" => ""]);
        }
    }
}

// function password_check($passwordParam, $passwordDb)
// {
//     $passwordEncoded = base64_encode($passwordParam);
//     if ($passwordEncoded == $passwordDb) {
//         $result = true;
//     } else {
//         $result = false;
//     }
//     return $result;
// }

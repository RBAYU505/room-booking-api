<?php
function getDB()
{
    $host = "aws-0-ap-southeast-1.pooler.supabase.com";
    $port = "5432";
    $dbname = "postgres";
    $user = "postgres.kcpgqmpfgiltwyzbriwm";
    $password = "123456Yes*";
    $sslmode = "require";

    $conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password sslmode=$sslmode";
    $db = pg_connect($conn_string);


    if (!$db) {
        die("Gagal terhubung ke database");
    }
    return $db;
}

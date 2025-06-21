<?php
function getDB()
{
    $host = "db.kcpgqmpfgiltwyzbriwm.supabase.co";
    $port = "5432";
    $dbname = "postgres";
    $user = "postgres";
    $password = "123456Yes*";

    $db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    if (!$db) {
        die("Gagal terhubung ke database");
    }
    return $db;
}

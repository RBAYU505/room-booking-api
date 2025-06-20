<?php
function getDB()
{
    $host = "db-host";
    $port = "5432";
    $dbname = "db-name";
    $user = "db-user";
    $password = "db-password";

    $db = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
    if (!$db) {
        die("Gagal terhubung ke database");
    }
    return $db;
}

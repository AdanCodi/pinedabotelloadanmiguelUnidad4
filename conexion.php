<?php

$host = getenv('DB_HOST') ?: '127.0.0.1';
$base = getenv('DB_NAME') ?: 'barberia_u4';
$usuario = getenv('DB_USER') ?: 'root';
$clave = getenv('DB_PASSWORD') ?: '';

$conexion = new PDO(
    "mysql:host=$host;dbname=$base;charset=utf8mb4",
    $usuario,
    $clave,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
<?php

$configFile = __DIR__ . '/conexion.local.php';

if (!file_exists($configFile)) {
    exit("Falta la configuración de conexión. Ejecuta instalar.bat.");
}

$config = require $configFile;

$host = $config['host'];
$puerto = $config['puerto'];
$base_datos = $config['base_datos'];
$usuario = $config['usuario'];
$contrasena = $config['contrasena'];

try {
    $conexion = new PDO(
        "mysql:host=$host;port=$puerto;dbname=$base_datos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $conexion->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("No se pudo conectar con la base de datos.");
}

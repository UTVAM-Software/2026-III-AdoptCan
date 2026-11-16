

<?php

$configLocal = __DIR__ . '/conexion.local.php';

if (!file_exists($configLocal)) {
    exit("No se encontró la configuración de la base de datos.");
}

$config = require $configLocal;

try {

    $host = $config['host'];
    $puerto = $config['puerto'];
    $base_datos = $config['base_datos'];
    $usuario = $config['usuario'];
    $contrasena = $config['contrasena'];

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

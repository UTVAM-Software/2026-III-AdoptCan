<?php

require_once "config/conexion.php";

echo "¡Conexión exitosa con NutriSalud!";

$consulta = $conexion->query("SHOW TABLES");

echo "<h3>Tablas de la base de datos:</h3>";

while ($tabla = $consulta->fetch(PDO::FETCH_NUM)) {
    echo $tabla[0] . "<br>";
}


<?php

require_once __DIR__ . "/../config/conexion.php";

// Solo permitir solicitudes POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: servicios.php");
    exit;
}

// Validar identificador
$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    header("Location: servicios.php?mensaje=error");
    exit;
}

try {

    // Eliminación lógica: cambiar el servicio a inactivo
    $consulta = $conexion->prepare(
        "UPDATE servicios
         SET estado = 0
         WHERE id = :id"
    );

    $consulta->execute([
        ":id" => $id
    ]);

    if ($consulta->rowCount() > 0) {
        header("Location: servicios.php?mensaje=inactivado");
    } else {
        header("Location: servicios.php?mensaje=no_encontrado");
    }

    exit;

} catch (PDOException $e) {

    error_log($e->getMessage());

    header("Location: servicios.php?mensaje=error");
    exit;
}

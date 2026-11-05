<?php

require_once "../config/conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: citas.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: citas.php");
    exit;
}

try {
    $sql = "UPDATE citas
            SET estado = 'Cancelada'
            WHERE id = :id";

    $consulta = $conexion->prepare($sql);
    $consulta->execute([
        ":id" => $id
    ]);

    header("Location: cita_detalle.php?id=" . $id . "&mensaje=cancelada");
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("No fue posible cancelar la cita.");
}
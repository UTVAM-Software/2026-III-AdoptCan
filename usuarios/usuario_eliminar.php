

<?php
require_once __DIR__ . '/../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id === 1) {
    header('Location: usuarios.php?error=protegido');
    exit;
}

try {
    // Desactivar usuario sin borrar sus registros
    $stmt = $conexion->prepare(
        "UPDATE usuarios SET estado = 0 WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: usuarios.php?mensaje=desactivado');
    exit;

} catch (PDOException $e) {
    error_log($e->getMessage());
    header('Location: usuarios.php?error=desactivar');
    exit;
}

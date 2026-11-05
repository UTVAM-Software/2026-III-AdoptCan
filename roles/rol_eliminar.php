

<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Verificar permisos
verificarPermiso('gestionar_roles');

// Solo aceptar solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: roles.php');
    exit;
}

// Validar ID recibido
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: roles.php?error=id_invalido');
    exit;
}

try {

    // Consultar el rol
    $consulta = $conexion->prepare(
        "SELECT id, nombre FROM roles WHERE id = ?"
    );

    $consulta->execute([$id]);
    $rol = $consulta->fetch(PDO::FETCH_ASSOC);

    if (!$rol) {
        header('Location: roles.php?error=rol_no_existe');
        exit;
    }

    // Proteger los roles principales
    if (in_array($rol['nombre'], ['Administrador', 'Empleado'], true)) {
        header('Location: roles.php?error=rol_protegido');
        exit;
    }

    // Comprobar usuarios asignados
    $consulta = $conexion->prepare(
        "SELECT COUNT(*) FROM usuarios WHERE rol_id = ?"
    );

    $consulta->execute([$id]);

    if ($consulta->fetchColumn() > 0) {
        header('Location: roles.php?error=rol_con_usuarios');
        exit;
    }

    // Eliminar el rol y sus permisos
    $conexion->beginTransaction();

    $consulta = $conexion->prepare(
        "DELETE FROM rol_permiso WHERE rol_id = ?"
    );

    $consulta->execute([$id]);

    $consulta = $conexion->prepare(
        "DELETE FROM roles WHERE id = ?"
    );

    $consulta->execute([$id]);

    $conexion->commit();

    header('Location: roles.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {

    if ($conexion->inTransaction()) {
        $conexion->rollBack();
    }

    error_log($e->getMessage());

    header('Location: roles.php?error=eliminacion');
    exit;
}

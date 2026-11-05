
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que exista una sesión iniciada
 * y que el usuario siga activo.
 */
function verificarSesion() {

    global $conexion;

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: /2026-III-AdoptCan/login.php");
        exit;
    }

    $sql = "SELECT estado
            FROM usuarios
            WHERE id = ?
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->execute([$_SESSION['usuario_id']]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario || (int)$usuario['estado'] !== 1) {
        session_unset();
        session_destroy();

        header("Location: /2026-III-AdoptCan/login.php");
        exit;
    }
}

/**
 * Comprueba si el usuario tiene un permiso.
 */
function tienePermiso($permiso) {

    global $conexion;

    if (!isset($_SESSION['usuario_id'])) {
        return false;
    }

    $sql = "SELECT COUNT(*)
            FROM usuarios u
            INNER JOIN rol_permiso rp
                ON u.rol_id = rp.rol_id
            INNER JOIN permisos p
                ON rp.permiso_id = p.id
            WHERE u.id = ?
              AND u.estado = 1
              AND p.nombre = ?";

    $stmt = $conexion->prepare($sql);

    $stmt->execute([
        $_SESSION['usuario_id'],
        $permiso
    ]);

    return (int)$stmt->fetchColumn() > 0;
}

/**
 * Impide el acceso si no tiene el permiso.
 */
function verificarPermiso($permiso) {

    verificarSesion();

    if (!tienePermiso($permiso)) {

        http_response_code(403);

        exit("Acceso denegado: no tienes permiso para acceder a este módulo.");
    }
}

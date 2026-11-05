
<?php
require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/auth.php';

verificarSesion();

// Obtener los permisos del usuario
$sql = "SELECT p.nombre
        FROM usuarios u
        INNER JOIN rol_permiso rp ON u.rol_id = rp.rol_id
        INNER JOIN permisos p ON rp.permiso_id = p.id
        WHERE u.id = ? AND u.estado = 1";

$stmt = $conexion->prepare($sql);
$stmt->execute([$_SESSION['usuario_id']]);
$permisos = $stmt->fetchAll(PDO::FETCH_COLUMN);

function puede($permiso, $permisos) {
    return in_array($permiso, $permisos, true);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 0;
        }

        header {
            background: #24765b;
            color: white;
            padding: 20px;
        }

        main {
            padding: 30px;
        }

        .modulos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .modulo {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-decoration: none;
            color: #24765b;
            box-shadow: 0 3px 10px #0001;
        }

        .modulo:hover {
            background: #e5f2ec;
        }

        .salir {
    color: white;
    background: #c0392b;
    float: right;
    text-decoration: none;
    padding: 10px 15px;
    border-radius: 5px;
    font-weight: bold;
}

.salir:hover {
    background: #922b21;
}
    </style>
</head>

<body>

<header>
    <a class="salir" href="logout.php">Cerrar sesión</a>

    <h1>NutriSalud</h1>

    <p>
        Bienvenido,
        <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
    </p>

    <p>
        Rol:
        <?= htmlspecialchars($_SESSION['rol_nombre']) ?>
    </p>
</header>

<main>

    <h2>Panel principal</h2>

    <div class="modulos">

        <?php if (puede('gestionar_usuarios', $permisos)): ?>
            <a class="modulo" href="usuarios/usuarios.php">
                <h3>Usuarios</h3>
                <p>Gestionar usuarios del sistema.</p>
            </a>
        <?php endif; ?>

        <?php if (puede('gestionar_roles', $permisos)): ?>
            <a class="modulo" href="roles/roles.php">
                <h3>Roles</h3>
                <p>Administrar roles.</p>
            </a>
        <?php endif; ?>

        <?php if (puede('gestionar_permisos', $permisos)): ?>
            <a class="modulo" href="roles/roles.php">
                <h3>Permisos</h3>
                <p>Consultar y administrar permisos.</p>
            </a>
        <?php endif; ?>

        <?php if (puede('gestionar_servicios', $permisos)): ?>
            <a class="modulo" href="servicios/servicios.php">
                <h3>Servicios</h3>
                <p>Gestionar servicios nutricionales.</p>
            </a>
        <?php endif; ?>

        <?php if (puede('gestionar_citas', $permisos)): ?>
            <a class="modulo" href="citas/citas.php">
                <h3>Citas</h3>
                <p>Gestionar citas.</p>
            </a>
        <?php endif; ?>

        <?php if (puede('gestionar_consejos', $permisos)): ?>
            <a class="modulo" href="solicitudes/solicitudes_consejo.php">
                <h3>Solicitudes de consejos</h3>
                <p>Gestionar solicitudes.</p>
            </a>
        <?php endif; ?>

    </div>

</main>

</body>
</html>



<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Verificar permisos
verificarPermiso('gestionar_roles');

// Consultar roles y cantidad de permisos
$sql = "SELECT r.id, r.nombre, r.descripcion,
               COUNT(rp.permiso_id) AS total_permisos
        FROM roles r
        LEFT JOIN rol_permiso rp ON r.id = rp.rol_id
        GROUP BY r.id, r.nombre, r.descripcion
        ORDER BY r.id ASC";

$stmt = $conexion->query($sql);
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mensajes
$mensajes = [
    'eliminado' => 'El rol se eliminó correctamente.',
    'actualizado' => 'El rol se actualizó correctamente.',
    'registrado' => 'El rol se registró correctamente.',
    'permisos_actualizados' => 'Los permisos se actualizaron correctamente.'
];

$errores = [
    'id_invalido' => 'El ID del rol no es válido.',
    'rol_no_existe' => 'El rol no existe.',
    'rol_protegido' => 'No puedes eliminar los roles principales del sistema.',
    'rol_con_usuarios' => 'No se puede eliminar un rol que tiene usuarios asignados.',
    'eliminacion' => 'Ocurrió un error al eliminar el rol.'
];

$mensaje = $mensajes[$_GET['mensaje'] ?? ''] ?? '';
$error = $errores[$_GET['error'] ?? ''] ?? '';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Roles - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 30px;
        }

        h1 {
            color: #24765b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #24765b;
            color: white;
        }

        a {
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin: 2px;
        }

        .nuevo {
            background: #24765b;
            color: white;
            padding: 9px 14px;
        }

        .editar {
            background: #e5a323;
            color: white;
        }

        .permisos {
            background: #24765b;
            color: white;
        }

        .eliminar {
            background: #dc3545;
            color: white;
            padding: 7px 12px;
            border-radius: 4px;
        }

        button:hover, a:hover {
            opacity: 0.85;
        }

        .mensaje {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-top: 15px;
        }

        .protegido {
            color: #777;
            font-size: 13px;
        }

        .acciones form {
            display: inline;
        }
    </style>

</head>

<body>

<h1>Gestión de Roles - NutriSalud</h1>

<?php if ($mensaje !== ''): ?>
    <div class="mensaje">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <div class="error">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
    </div>
<?php endif; ?>

<a class="nuevo" href="rol_nuevo.php">
    + Registrar nuevo rol
</a>

<table>

    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del rol</th>
            <th>Descripción</th>
            <th>Total de permisos</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>

    <?php if (count($roles) > 0): ?>

        <?php foreach ($roles as $rol): ?>

            <tr>

                <td>
                    <?= (int)$rol['id'] ?>
                </td>

                <td>
                    <?= htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8') ?>
                </td>

                <td>
                    <?= htmlspecialchars($rol['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                </td>

                <td>
                    <?= (int)$rol['total_permisos'] ?>
                </td>

                <td class="acciones">

                    <a class="editar"
                       href="rol_editar.php?id=<?= (int)$rol['id'] ?>">
                        Editar
                    </a>

                    <a class="permisos"
                       href="rol_permisos.php?id=<?= (int)$rol['id'] ?>">
                        Permisos
                    </a>

                    <?php if (!in_array(
                        $rol['nombre'],
                        ['Administrador', 'Empleado'],
                        true
                    )): ?>

                        <form method="POST"
                              action="rol_eliminar.php"
                              onsubmit="return confirm('¿Estás seguro de que deseas eliminar este rol? Esta acción no se puede deshacer.');">

                            <input type="hidden"
                                   name="id"
                                   value="<?= (int)$rol['id'] ?>">

                            <button type="submit" class="eliminar">
                                Eliminar
                            </button>

                        </form>

                    <?php else: ?>

                        <span class="protegido">
                            Rol protegido
                        </span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

    <?php else: ?>

        <tr>
            <td colspan="5">
                No hay roles registrados.
            </td>
        </tr>

    <?php endif; ?>

    </tbody>

</table>

<p>
    <a href="../index.php">Volver al panel principal</a>
</p>

</body>
</html>

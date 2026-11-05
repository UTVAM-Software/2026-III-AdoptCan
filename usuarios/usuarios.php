
<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Verificar que el usuario tenga permiso para gestionar usuarios
verificarPermiso('gestionar_usuarios');

// Consultar usuarios y sus roles

// Consultar usuarios y sus roles
$sql = "SELECT u.id, u.nombre, u.correo, u.estado,
               u.fecha_registro, r.nombre AS rol
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        ORDER BY u.id DESC";

$stmt = $conexion->query($sql);
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios - NutriSalud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 30px;
        }
        h1 { color: #24765b; }
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
        a, button {
            padding: 7px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .nuevo {
            background: #24765b;
            color: white;
        }
        .editar {
            background: #e5a323;
            color: white;
        }
        .estado {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Gestión de Usuarios - NutriSalud</h1>

<a class="nuevo" href="usuario_nuevo.php">+ Registrar usuario</a>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            <th>Fecha de registro</th>
            <th>Acciones</th>
        </tr>
    </thead>

    <tbody>
    <?php if (count($usuarios) > 0): ?>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= htmlspecialchars($usuario['id']) ?></td>
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['correo']) ?></td>
                <td><?= htmlspecialchars($usuario['rol']) ?></td>
                <td class="estado">
                    <?= $usuario['estado'] ? 'Activo' : 'Inactivo' ?>
                </td>
                <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                <td>
                    <a class="editar"
   href="usuario_editar.php?id=<?= $usuario['id'] ?>">
   Editar
</a>

<?php if ((int)$usuario['id'] !== 1 && (int)$usuario['estado'] === 1): ?>
    <form method="POST" action="usuario_eliminar.php"
          style="display:inline;"
          onsubmit="return confirm('¿Deseas desactivar este usuario?');">

        <input type="hidden" name="id"
               value="<?= (int)$usuario['id'] ?>">

        <button type="submit"
                style="background:#c0392b;color:white;border:none;cursor:pointer;">
            Desactivar
        </button>
    </form>
<?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7">Todavía no hay usuarios registrados.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>


<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Proteger el módulo
verificarPermiso('gestionar_roles');

// Verificar el ID recibido
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    exit("ID de rol no válido.");
}

$id = (int) $_GET['id'];

// Consultar el rol
$sql = "SELECT * FROM roles WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$id]);

$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    exit("El rol no existe.");
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $mensaje = 'El nombre del rol es obligatorio.';

    } elseif (mb_strlen($nombre) > 50) {
        $mensaje = 'El nombre no puede superar los 50 caracteres.';

    } elseif (mb_strlen($descripcion) > 150) {
        $mensaje = 'La descripción no puede superar los 150 caracteres.';

    } else {

        try {
            $sql = "UPDATE roles
                    SET nombre = ?,
                        descripcion = ?
                    WHERE id = ?";

            $stmt = $conexion->prepare($sql);

            $stmt->execute([
                $nombre,
                $descripcion !== '' ? $descripcion : null,
                $id
            ]);

            header('Location: roles.php?mensaje=actualizado');
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $mensaje = 'Ya existe otro rol con ese nombre.';
            } else {
                error_log($e->getMessage());
                $mensaje = 'No se pudo actualizar el rol.';
            }
        }
    }

    // Mantener los datos introducidos
    $rol['nombre'] = $nombre;
    $rol['descripcion'] = $descripcion;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Rol - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 30px;
        }

        h1 {
            color: #24765b;
        }

        form {
            background: white;
            padding: 25px;
            max-width: 500px;
            border-radius: 8px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 7px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            margin-top: 20px;
            padding: 10px 18px;
            background: #24765b;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #19563f;
        }

        .volver {
            display: inline-block;
            margin-top: 20px;
            color: #24765b;
            text-decoration: none;
        }

        .mensaje {
            color: #dc3545;
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

<h1>Editar Rol - NutriSalud</h1>

<?php if ($mensaje !== ''): ?>
    <p class="mensaje">
        <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label for="nombre">Nombre del rol:</label>

    <input
        type="text"
        id="nombre"
        name="nombre"
        maxlength="50"
        value="<?= htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8') ?>"
        required
    >

    <label for="descripcion">Descripción:</label>

    <textarea
        id="descripcion"
        name="descripcion"
        maxlength="150"
        rows="4"
    ><?= htmlspecialchars($rol['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

    <button type="submit">Guardar cambios</button>

</form>

<a class="volver" href="roles.php">
    ← Volver a gestión de roles
</a>

</body>
</html>


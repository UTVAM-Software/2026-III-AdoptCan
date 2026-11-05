

<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Proteger el registro de roles
verificarPermiso('gestionar_roles');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');

    if ($nombre === '') {
        $error = 'El nombre del rol es obligatorio.';

    } elseif (mb_strlen($nombre) > 50) {
        $error = 'El nombre no puede superar los 50 caracteres.';

    } elseif (mb_strlen($descripcion) > 150) {
        $error = 'La descripción no puede superar los 150 caracteres.';

    } else {
        try {
            $stmt = $conexion->prepare(
                "INSERT INTO roles (nombre, descripcion)
                 VALUES (?, ?)"
            );

            $stmt->execute([
                $nombre,
                $descripcion !== '' ? $descripcion : null
            ]);

            header('Location: roles.php?mensaje=registrado');
            exit;

        } catch (PDOException $e) {

            if ($e->getCode() === '23000') {
                $error = 'Ya existe un rol con ese nombre.';
            } else {
                error_log($e->getMessage());
                $error = 'No se pudo registrar el rol.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo rol - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 30px;
        }

        .contenedor {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            color: #24765b;
        }

        label {
            display: block;
            margin-top: 15px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            box-sizing: border-box;
        }

        button {
            background: #24765b;
            color: white;
            padding: 12px;
            border: none;
            margin-top: 20px;
            cursor: pointer;
            width: 100%;
        }

        button:hover {
            background: #19563f;
        }

        .error {
            color: #c0392b;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Registrar nuevo rol</h1>

    <?php if ($error): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre del rol:</label>
        <input
            type="text"
            name="nombre"
            maxlength="50"
            required
            value="<?= htmlspecialchars($_POST['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
        >

        <label>Descripción:</label>
        <textarea
            name="descripcion"
            maxlength="150"
            rows="4"
        ><?= htmlspecialchars($_POST['descripcion'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>

        <button type="submit">Guardar rol</button>

    </form>

    <p><a href="roles.php">Volver a roles</a></p>

</div>

</body>
</html>

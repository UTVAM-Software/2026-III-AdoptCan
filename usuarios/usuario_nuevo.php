
<?php
require_once __DIR__ . '/../config/conexion.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';
    $rol_id = (int)($_POST['rol_id'] ?? 0);

    if ($nombre === '' || $correo === '' || $contrasena === '' || $rol_id <= 0) {
        $mensaje = 'Todos los campos son obligatorios.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingresa un correo electrónico válido.';
    } elseif (strlen($contrasena) < 8) {
        $mensaje = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        try {
            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios
                    (nombre, correo, contrasena, rol_id, estado)
                    VALUES (?, ?, ?, ?, 1)";

            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                $nombre,
                $correo,
                $hash,
                $rol_id
            ]);

            header('Location: usuarios.php?registro=exitoso');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $mensaje = 'El correo ya está registrado.';
            } else {
                $mensaje = 'Error al registrar el usuario.';
                error_log($e->getMessage());
            }
        }
    }
}

// Obtener roles disponibles
$stmt = $conexion->query("SELECT id, nombre FROM roles ORDER BY id");
$roles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario - NutriSalud</title>
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

        h1 { color: #24765b; }

        label {
            display: block;
            margin-top: 15px;
        }

        input, select {
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

        .error { color: red; }
    </style>
</head>
<body>

<div class="contenedor">

    <h1>Registrar usuario</h1>

    <?php if ($mensaje): ?>
        <p class="error">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Nombre completo:</label>
        <input type="text" name="nombre" required maxlength="100">

        <label>Correo electrónico:</label>
        <input type="email" name="correo" required maxlength="120">

        <label>Contraseña:</label>
        <input type="password" name="contrasena"
               required minlength="8">

        <label>Rol:</label>
        <select name="rol_id" required>
            <option value="">Selecciona un rol</option>

            <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['id'] ?>">
                    <?= htmlspecialchars($rol['nombre']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <button type="submit">Registrar usuario</button>

    </form>

    <p>
        <a href="usuarios.php">Volver a usuarios</a>
    </p>

</div>

</body>
</html>

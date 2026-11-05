
<?php
require_once __DIR__ . '/config/conexion.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si ya inició sesión, ir al panel principal
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');
    $contrasena = $_POST['contrasena'] ?? '';

    if ($correo === '' || $contrasena === '') {

        $mensaje = 'Completa todos los campos.';

    } else {

        $sql = "SELECT u.id, u.nombre, u.correo,
                       u.contrasena, u.estado,
                       r.id AS rol_id,
                       r.nombre AS rol
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.correo = ?
                LIMIT 1";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([$correo]);

        $usuario = $stmt->fetch();

        if (
            $usuario &&
            $usuario['estado'] == 1 &&
            password_verify($contrasena, $usuario['contrasena'])
        ) {

            session_regenerate_id(true);

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_correo'] = $usuario['correo'];
            $_SESSION['rol_id'] = $usuario['rol_id'];
            $_SESSION['rol_nombre'] = $usuario['rol'];

            header('Location: index.php');
            exit;

        } else {

            $mensaje = 'Correo o contraseña incorrectos, o usuario inactivo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .contenedor {
            background: white;
            padding: 35px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,.1);
        }

        h1 {
            color: #24765b;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 18px;
        }

        input {
            width: 100%;
            padding: 11px;
            margin-top: 7px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 25px;
            background: #24765b;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .error {
            color: #c0392b;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>NutriSalud</h1>
    <p style="text-align:center">Iniciar sesión</p>

    <?php if ($mensaje !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($mensaje) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label>Correo electrónico:</label>
        <input type="email" name="correo"
               required maxlength="120">

        <label>Contraseña:</label>
        <input type="password" name="contrasena"
               required>

        <button type="submit">Ingresar</button>

    </form>

</div>

</body>
</html>


<?php
require_once "../config/conexion.php";

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: usuarios.php");
    exit;
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header("Location: usuarios.php");
    exit;
}

$roles = $conexion->query("SELECT id, nombre FROM roles ORDER BY nombre")->fetchAll();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $correo = trim($_POST["correo"] ?? "");
    $rol_id = (int)($_POST["rol_id"] ?? 0);
    $estado = (int)($_POST["estado"] ?? 1);

    if ($nombre === "" || $correo === "" || $rol_id <= 0) {
        $error = "Completa todos los campos.";
    } else {
        try {
            $stmt = $conexion->prepare(
                "UPDATE usuarios
                 SET nombre = ?, correo = ?, rol_id = ?, estado = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $nombre,
                $correo,
                $rol_id,
                $estado,
                $id
            ]);

            header("Location: usuarios.php");
            exit;

        } catch (PDOException $e) {
            $error = "No se pudo actualizar. Verifica que el correo no esté registrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario - NutriSalud</title>
</head>
<body>

<h1>Editar usuario</h1>

<?php if ($error): ?>
    <p style="color:red;">
        <?= htmlspecialchars($error) ?>
    </p>
<?php endif; ?>

<form method="POST">

    <label>Nombre:</label><br>
    <input type="text" name="nombre"
        value="<?= htmlspecialchars($_POST['nombre'] ?? $usuario['nombre']) ?>"
        required><br><br>

    <label>Correo:</label><br>
    <input type="email" name="correo"
        value="<?= htmlspecialchars($_POST['correo'] ?? $usuario['correo']) ?>"
        required><br><br>

    <label>Rol:</label><br>
    <select name="rol_id" required>
        <?php foreach ($roles as $rol): ?>
            <option value="<?= $rol['id'] ?>"
                <?= (int)($_POST['rol_id'] ?? $usuario['rol_id']) === (int)$rol['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($rol['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label>Estado:</label><br>
    <select name="estado">
        <option value="1" <?= (int)($_POST['estado'] ?? $usuario['estado']) === 1 ? 'selected' : '' ?>>
            Activo
        </option>
        <option value="0" <?= (int)($_POST['estado'] ?? $usuario['estado']) === 0 ? 'selected' : '' ?>>
            Inactivo
        </option>
    </select><br><br>

    <button type="submit">Guardar cambios</button>
    <a href="usuarios.php">Cancelar</a>

</form>

</body>
</html>

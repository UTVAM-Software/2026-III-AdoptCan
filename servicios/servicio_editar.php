<?php
require_once __DIR__ . "/../config/conexion.php";

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("ID de servicio no válido.");
}

$consulta = $conexion->prepare(
    "SELECT * FROM servicios WHERE id = :id"
);
$consulta->execute([':id' => $id]);
$servicio = $consulta->fetch();

if (!$servicio) {
    exit("El servicio no existe.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $estado = $_POST["estado"] ?? "";

    if ($nombre === "" || strlen($nombre) > 120) {
        $error = "El nombre es obligatorio y no debe superar 120 caracteres.";
    } elseif (!in_array($estado, ["0", "1"], true)) {
        $error = "Selecciona un estado válido.";
    } elseif (
        $precio !== "" &&
        (!is_numeric($precio) || $precio < 0 || $precio > 99999999.99)
    ) {
        $error = "El precio debe ser un número válido y no negativo.";
    } else {
        try {
            $actualizar = $conexion->prepare(
                "UPDATE servicios
                 SET nombre = :nombre,
                     descripcion = :descripcion,
                     precio = :precio,
                     estado = :estado
                 WHERE id = :id"
            );

            $actualizar->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion !== "" ? $descripcion : null,
                ':precio' => $precio !== "" ? $precio : null,
                ':estado' => (int)$estado,
                ':id' => $id
            ]);

            header("Location: servicios.php?mensaje=actualizado");
            exit;

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "No se pudo actualizar el servicio.";
        }
    }

    // Conserva los valores escritos si ocurre un error.
    $servicio["nombre"] = $nombre;
    $servicio["descripcion"] = $descripcion;
    $servicio["precio"] = $precio;
    $servicio["estado"] = (int)$estado;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar servicio - NutriSalud</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            margin: 0;
            padding: 30px;
        }

        .contenedor {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            margin-top: 6px;
        }

        button, .cancelar {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }

        button {
            background: #198754;
            color: white;
        }

        .cancelar {
            background: #6c757d;
            color: white;
        }

        .error {
            color: #b02a37;
        }
    </style>
</head>
<body>

<div class="contenedor">
    <h1>Editar servicio</h1>

    <?php if ($error !== ""): ?>
        <p class="error"><?= escapar($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nombre del servicio</label>
        <input
            type="text"
            name="nombre"
            maxlength="120"
            required
            value="<?= escapar($servicio['nombre']) ?>"
        >

        <label>Descripción</label>
        <textarea name="descripcion" rows="4"><?= escapar($servicio['descripcion']) ?></textarea>

        <label>Precio</label>
        <input
            type="number"
            name="precio"
            min="0"
            max="99999999.99"
            step="0.01"
            value="<?= escapar($servicio['precio']) ?>"
        >

        <label>Estado</label>
        <select name="estado" required>
            <option value="1" <?= (int)$servicio['estado'] === 1 ? 'selected' : '' ?>>
                Activo
            </option>
            <option value="0" <?= (int)$servicio['estado'] === 0 ? 'selected' : '' ?>>
                Inactivo
            </option>
        </select>

        <button type="submit">Guardar cambios</button>
        <a class="cancelar" href="servicios.php">Cancelar</a>
    </form>
</div>

</body>
</html>
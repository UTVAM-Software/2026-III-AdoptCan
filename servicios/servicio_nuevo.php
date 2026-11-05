
<?php
require_once "../config/conexion.php";

$error = "";
$nombre = "";
$descripcion = "";
$precio = "";
$estado = "1";

// Registrar servicio
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $precio = trim($_POST["precio"] ?? "");
    $estado = $_POST["estado"] ?? "1";

    // Validar nombre
    if ($nombre === "") {
        $error = "El nombre del servicio es obligatorio.";

    } elseif (strlen($nombre) > 120) {
        $error = "El nombre no puede superar los 120 caracteres.";

    } elseif (!in_array($estado, ["0", "1"], true)) {
        $error = "El estado seleccionado no es válido.";

    } elseif (
        $precio !== "" &&
        (!is_numeric($precio) || (float)$precio < 0 || (float)$precio > 99999999.99)
    ) {
        $error = "Ingresa un precio válido.";

    } else {

        try {

            $sql = "INSERT INTO servicios
                    (nombre, descripcion, precio, estado)
                    VALUES (:nombre, :descripcion, :precio, :estado)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                ":nombre" => $nombre,
                ":descripcion" => $descripcion !== "" ? $descripcion : null,
                ":precio" => $precio !== "" ? $precio : null,
                ":estado" => (int)$estado
            ]);

            // Regresar al listado
            header("Location: servicios.php?mensaje=registrado");
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $error = "No fue posible registrar el servicio.";
        }
    }
}

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrar Servicio | NutriSalud</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f6;
            padding: 30px;
            color: #333;
        }

        .contenedor {
            max-width: 650px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        h1 {
            color: #237a57;
            margin-bottom: 10px;
        }

        .descripcion {
            color: #777;
            margin-bottom: 25px;
        }

        .grupo {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
            min-height: 110px;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #237a57;
        }

        .botones {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .boton {
            padding: 12px 18px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .guardar {
            background: #237a57;
        }

        .cancelar {
            background: #777;
        }

        .error {
            background: #fce4e4;
            color: #a94442;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        @media (max-width: 600px) {
            body {
                padding: 12px;
            }

            .contenedor {
                padding: 20px;
            }
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Registrar servicio</h1>

    <p class="descripcion">
        Completa los datos para agregar un nuevo servicio nutricional.
    </p>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">

        <div class="grupo">
            <label for="nombre">Nombre del servicio *</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                maxlength="120"
                value="<?= escapar($nombre) ?>"
                placeholder="Ej. Consulta nutricional"
                required>
        </div>

        <div class="grupo">
            <label for="descripcion">Descripción</label>

            <textarea
                id="descripcion"
                name="descripcion"
                placeholder="Describe en qué consiste el servicio..."><?= escapar($descripcion) ?></textarea>
        </div>

        <div class="grupo">
            <label for="precio">Precio</label>

            <input
                type="number"
                id="precio"
                name="precio"
                min="0"
                max="99999999.99"
                step="0.01"
                value="<?= escapar($precio) ?>"
                placeholder="Ej. 350.00">
        </div>

        <div class="grupo">
            <label for="estado">Estado</label>

            <select id="estado" name="estado">

                <option value="1" <?= $estado === "1" ? "selected" : "" ?>>
                    Activo
                </option>

                <option value="0" <?= $estado === "0" ? "selected" : "" ?>>
                    Inactivo
                </option>

            </select>
        </div>

        <div class="botones">

            <button type="submit" class="boton guardar">
                Guardar servicio
            </button>

            <a href="servicios.php" class="boton cancelar">
                Cancelar
            </a>

        </div>

    </form>

</div>

</body>
</html>

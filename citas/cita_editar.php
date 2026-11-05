
<?php
require_once __DIR__ . "/../config/conexion.php";

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

// Validar ID
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Identificador de cita no válido.");
}

// Consultar cita
$consulta = $conexion->prepare(
    "SELECT * FROM citas WHERE id = :id"
);

$consulta->execute([":id" => $id]);
$cita = $consulta->fetch();

if (!$cita) {
    exit("No se encontró la cita.");
}

$error = "";

$estadosPermitidos = [
    "Pendiente",
    "Confirmada",
    "Cancelada",
    "Completada"
];

// Actualizar estado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $estado = $_POST["estado"] ?? "";

    if (!in_array($estado, $estadosPermitidos, true)) {

        $error = "Selecciona un estado válido.";

    } else {

        try {

            $actualizar = $conexion->prepare(
                "UPDATE citas
                 SET estado = :estado
                 WHERE id = :id"
            );

            $actualizar->execute([
                ":estado" => $estado,
                ":id" => $id
            ]);

            header("Location: citas.php?mensaje=actualizada");
            exit;

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $error = "No se pudo actualizar la cita.";
        }
    }

    $cita["estado"] = $estado;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Actualizar cita | NutriSalud</title>

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7f6;
            padding: 30px;
            color: #333;
        }

        .contenedor {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
        }

        h1 {
            color: #237a57;
            margin-bottom: 15px;
        }

        .informacion {
            background: #f4f7f6;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .botones {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        button, .cancelar {
            padding: 12px 17px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            cursor: pointer;
            color: white;
        }

        button {
            background: #237a57;
        }

        .cancelar {
            background: #777;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

<div class="contenedor">

    <h1>Actualizar cita</h1>

    <div class="informacion">

        <p>
            <strong>Cita:</strong>
            #<?= (int)$cita["id"] ?>
        </p>

        <p>
            <strong>Paciente:</strong>
            <?= escapar($cita["nombre"]) ?>
        </p>

        <p>
            <strong>Correo:</strong>
            <?= escapar($cita["correo"]) ?>
        </p>

        <p>
            <strong>Estado actual:</strong>
            <?= escapar($cita["estado"]) ?>
        </p>

    </div>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label for="estado">
            Seleccionar nuevo estado
        </label>

        <select name="estado" id="estado" required>

            <?php foreach ($estadosPermitidos as $estado): ?>

                <option
                    value="<?= escapar($estado) ?>"
                    <?= $cita["estado"] === $estado ? "selected" : "" ?>>

                    <?= escapar($estado) ?>

                </option>

            <?php endforeach; ?>

        </select>

        <div class="botones">

            <button type="submit">
                Guardar cambios
            </button>

            <a href="citas.php" class="cancelar">
                Cancelar
            </a>

        </div>

    </form>

</div>

</body>
</html>

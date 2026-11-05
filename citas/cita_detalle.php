
<?php
require_once __DIR__ . "/../config/conexion.php";

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id) {
    exit("El identificador de la cita no es válido.");
}

try {
    $sql = "SELECT
                c.id,
                c.nombre,
                c.correo,
                c.telefono,
                c.fecha_preferida,
                c.mensaje,
                c.estado,
                c.fecha_solicitud,
                s.nombre AS servicio_nombre,
                s.precio AS servicio_precio
            FROM citas c
            LEFT JOIN servicios s ON c.servicio_id = s.id
            WHERE c.id = :id";

    $consulta = $conexion->prepare($sql);
    $consulta->execute(["id" => $id]);

    $cita = $consulta->fetch();

    if (!$cita) {
        exit("No se encontró la cita solicitada.");
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    exit("No fue posible consultar los detalles de la cita.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Cita | NutriSalud</title>

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
            max-width: 750px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
        }

        h1 {
            color: #237a57;
            margin-bottom: 25px;
        }

        .dato {
            padding: 13px 0;
            border-bottom: 1px solid #eee;
        }

        .dato strong {
            display: block;
            color: #555;
            margin-bottom: 6px;
        }

        .acciones {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .boton {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background: #237a57;
        }

        .secundario {
            background: #6c757d;
        }

        @media(max-width: 600px) {
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

        <h1>Detalle de la Cita #<?= (int)$cita["id"] ?></h1>

        <div class="dato">
            <strong>Nombre del paciente</strong>
            <?= escapar($cita["nombre"]) ?>
        </div>

        <div class="dato">
            <strong>Correo electrónico</strong>
            <?= escapar($cita["correo"]) ?>
        </div>

        <div class="dato">
            <strong>Teléfono</strong>
            <?= escapar($cita["telefono"] ?: "No proporcionado") ?>
        </div>

        <div class="dato">
            <strong>Servicio solicitado</strong>
            <?= escapar($cita["servicio_nombre"] ?? "Sin servicio asignado") ?>
        </div>

        <div class="dato">
            <strong>Precio del servicio</strong>
            <?= $cita["servicio_precio"] !== null
                ? "$" . number_format((float)$cita["servicio_precio"], 2)
                : "No disponible" ?>
        </div>

        <div class="dato">
            <strong>Fecha preferida</strong>
            <?= escapar($cita["fecha_preferida"] ?: "No especificada") ?>
        </div>

        <div class="dato">
            <strong>Mensaje del paciente</strong>
            <?= nl2br(escapar($cita["mensaje"] ?: "Sin mensaje")) ?>
        </div>

        <div class="dato">
            <strong>Estado de la cita</strong>
            <?= escapar($cita["estado"]) ?>
        </div>

        <div class="dato">
            <strong>Fecha de solicitud</strong>
            <?= escapar($cita["fecha_solicitud"]) ?>
        </div>
<?php if (isset($_GET["mensaje"]) && $_GET["mensaje"] === "cancelada"): ?>
    <p style="color: green;">
        La cita se canceló correctamente.
    </p>
<?php endif; ?>
        <div class="acciones">
            <a class="boton"
            <?php if ($cita["estado"] !== "Cancelada"): ?>
    <form action="cita_cancelar.php" method="POST"
          onsubmit="return confirm('¿Estás seguro de cancelar esta cita?');">

        <input type="hidden" name="id"
               value="<?php echo $cita['id']; ?>">

        <button type="submit">
            Cancelar cita
        </button>
    </form>
<?php endif; ?>
               href="cita_editar.php?id=<?= (int)$cita["id"] ?>">
                Cambiar estado
            </a>

            <a class="boton secundario" href="citas.php">
                Volver al listado
            </a>
        </div>

    </div>
</body>
</html>

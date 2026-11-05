
<?php
require_once __DIR__ . "/../config/conexion.php";

function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

try {
    $sql = "SELECT
                c.id,
                c.nombre,
                c.correo,
                c.telefono,
                c.fecha_preferida,
                c.estado,
                c.fecha_solicitud,
                s.nombre AS servicio_nombre
            FROM citas c
            LEFT JOIN servicios s ON c.servicio_id = s.id
            ORDER BY c.fecha_solicitud DESC";

    $consulta = $conexion->prepare($sql);
    $consulta->execute();

    $citas = $consulta->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $citas = [];
    $error = "No fue posible consultar las citas.";
}

function claseEstado($estado) {
    switch ($estado) {
        case "Pendiente":
            return "pendiente";
        case "Confirmada":
            return "confirmada";
        case "Cancelada":
            return "cancelada";
        case "Completada":
            return "completada";
        default:
            return "otro";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Citas | NutriSalud</title>

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
            max-width: 1200px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,.08);
        }

        h1 {
            color: #237a57;
            margin-bottom: 10px;
        }

        .descripcion {
            color: #777;
            margin-bottom: 25px;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #237a57;
            color: white;
        }

        tr:hover {
            background: #f7faf8;
        }

        .estado {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .pendiente {
            background: #fff3cd;
            color: #856404;
        }

        .confirmada {
            background: #d1e7dd;
            color: #0f5132;
        }

        .cancelada {
            background: #f8d7da;
            color: #842029;
        }

        .completada {
            background: #cfe2ff;
            color: #084298;
        }

        .otro {
            background: #e2e3e5;
            color: #41464b;
        }

        .boton {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background: #237a57;
            font-size: 13px;
        }

        .sin-datos {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .error {
            background: #f8d7da;
            color: #842029;
            padding: 12px;
            border-radius: 6px;
        }

        @media(max-width: 600px) {
            body {
                padding: 12px;
            }

            .contenedor {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="contenedor">

    <h1>Gestión de Citas</h1>

    <p class="descripcion">
        Consulta y administra las citas de los pacientes de NutriSalud.
    </p>

    <?php if (isset($error)): ?>
        <p class="error"><?= escapar($error) ?></p>
    <?php endif; ?>
<?php if (isset($_GET["mensaje"]) && $_GET["mensaje"] === "actualizada"): ?>

    <div style="background:#d1e7dd; color:#0f5132;
                padding:12px; border-radius:6px; margin-bottom:15px;">
        La cita se actualizó correctamente.
    </div>

<?php endif; ?>
    <div class="tabla-contenedor">

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Paciente</th>
                    <th>Correo</th>
                    <th>Servicio</th>
                    <th>Fecha preferida</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($citas) > 0): ?>

                    <?php foreach ($citas as $cita): ?>

                        <tr>
                            <td><?= (int)$cita["id"] ?></td>

                            <td><?= escapar($cita["nombre"]) ?></td>

                            <td><?= escapar($cita["correo"]) ?></td>

                            <td>
                                <?= escapar($cita["servicio_nombre"] ?? "Sin servicio") ?>
                            </td>

                            <td>
                                <?= escapar($cita["fecha_preferida"] ?? "No especificada") ?>
                            </td>

                            <td>
                                <span class="estado <?= claseEstado($cita["estado"]) ?>">
                                    <?= escapar($cita["estado"]) ?>
                                </span>
                            </td>

                            <td>
                                <a
                                    class="boton"
                                    href="cita_detalle.php?id=<?= (int)$cita["id"] ?>">
                                    Ver detalles
                                </a>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="7" class="sin-datos">
                            No hay citas registradas todavía.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>


<?php
require_once "../config/conexion.php";

try {
    // Consultar todos los servicios
    $consulta = $conexion->prepare(
        "SELECT id, nombre, descripcion, precio, estado, fecha_creacion
         FROM servicios
         ORDER BY id DESC"
    );

    $consulta->execute();
    $servicios = $consulta->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "No fue posible consultar los servicios.";
    $servicios = [];
}

// Función para proteger los datos mostrados en HTML
function escapar($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Servicios | NutriSalud</title>

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
            max-width: 1150px;
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

        .boton {
            display: inline-block;
            padding: 11px 17px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .nuevo {
            background: #237a57;
            margin-bottom: 20px;
        }

        .editar {
            background: #e5a52c;
        }

        .eliminar {
            background: #d9534f;
        }

        .tabla-contenedor {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 14px;
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

        .activo {
            color: #237a57;
            font-weight: bold;
        }

        .inactivo {
            color: #d9534f;
            font-weight: bold;
        }

        .acciones {
            display: flex;
            gap: 7px;
            align-items: center;
        }

        .acciones form {
            margin: 0;
        }

        .sin-datos {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .mensaje-error {
            background: #fce4e4;
            color: #a94442;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

        @media (max-width: 600px) {
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

    <h1>Gestión de Servicios</h1>

    <p class="descripcion">
        Administración de los servicios nutricionales de NutriSalud.
    </p>

    <a href="servicio_nuevo.php" class="boton nuevo">
        + Registrar nuevo servicio
    </a>

    <?php if (isset($error)): ?>
        <div class="mensaje-error">
            <?= escapar($error) ?>
        </div>
    <?php endif; ?>
<?php if (isset($_GET["mensaje"])): ?>

    <?php
    $mensajes = [
        "registrado" => "Servicio registrado correctamente.",
        "actualizado" => "Servicio actualizado correctamente.",
        "inactivado" => "Servicio desactivado correctamente.",
        "no_encontrado" => "No se encontró el servicio.",
        "error" => "Ocurrió un error al procesar la operación."
    ];

    $mensaje = $mensajes[$_GET["mensaje"]] ?? "";
    ?>

    <?php if ($mensaje !== ""): ?>
        <div style="padding:12px; margin-bottom:15px;
                    background:#e2f3e9; color:#237a57;
                    border-radius:6px;">
            <?= escapar($mensaje) ?>
        </div>
    <?php endif; ?>

<?php endif; ?>      
    <div class="tabla-contenedor">

        <table>

            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($servicios) > 0): ?>

                    <?php foreach ($servicios as $servicio): ?>

                        <tr>

                            <td>
                                <?= escapar($servicio["id"]) ?>
                            </td>

                            <td>
                                <?= escapar($servicio["nombre"]) ?>
                            </td>

                            <td>
                                <?= escapar($servicio["descripcion"] ?? "") ?>
                            </td>

                            <td>
                                <?php if ($servicio["precio"] !== null): ?>
                                    $<?= number_format((float)$servicio["precio"], 2) ?>
                                <?php else: ?>
                                    Sin precio
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($servicio["estado"] == 1): ?>
                                    <span class="activo">Activo</span>
                                <?php else: ?>
                                    <span class="inactivo">Inactivo</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="acciones">

                                    <a
                                        href="servicio_editar.php?id=<?= (int)$servicio["id"] ?>"
                                        class="boton editar">
                                        Editar
                                    </a>

                                    <form
                                        action="servicio_eliminar.php"
                                        method="POST"
                                        onsubmit="return confirm('¿Estás seguro de eliminar este servicio?');">

                                        <input
                                            type="hidden"
                                            name="id"
                                            value="<?= (int)$servicio["id"] ?>">

                                        <button
                                            type="submit"
                                            class="boton eliminar">
                                            Eliminar
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="6" class="sin-datos">
                            No hay servicios registrados todavía.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>


<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/auth.php';

// Proteger el módulo
verificarPermiso('gestionar_permisos');

// Validar ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: roles.php');
    exit;
}

// Consultar rol
$stmt = $conexion->prepare(
    "SELECT id, nombre, descripcion FROM roles WHERE id = ?"
);
$stmt->execute([$id]);

$rol = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$rol) {
    header('Location: roles.php');
    exit;
}

// Obtener permisos disponibles
$permisos = $conexion->query(
    "SELECT id, nombre, descripcion
     FROM permisos
     ORDER BY id"
)->fetchAll(PDO::FETCH_ASSOC);

$error = '';

// Obtener permisos actualmente asignados
$stmt = $conexion->prepare(
    "SELECT permiso_id
     FROM rol_permiso
     WHERE rol_id = ?"
);
$stmt->execute([$id]);

$asignados = array_map(
    'intval',
    $stmt->fetchAll(PDO::FETCH_COLUMN)
);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $seleccionados = $_POST['permisos'] ?? [];

    if (!is_array($seleccionados)) {
        $seleccionados = [];
    }

    $seleccionados = array_unique(
        array_filter(
            array_map('intval', $seleccionados),
            fn($permisoId) => $permisoId > 0
        )
    );

    // Comprobar que todos los permisos enviados existan
    $idsDisponibles = array_map(
        'intval',
        array_column($permisos, 'id')
    );

    if (count(array_diff($seleccionados, $idsDisponibles)) > 0) {

        $error = 'Se enviaron permisos no válidos.';

    } else {

        try {
            $conexion->beginTransaction();

            // Eliminar permisos anteriores
            $stmt = $conexion->prepare(
                "DELETE FROM rol_permiso WHERE rol_id = ?"
            );
            $stmt->execute([$id]);

            // Guardar los nuevos permisos
            $stmt = $conexion->prepare(
                "INSERT INTO rol_permiso (rol_id, permiso_id)
                 VALUES (?, ?)"
            );

            foreach ($seleccionados as $permisoId) {
                $stmt->execute([$id, $permisoId]);
            }

            $conexion->commit();

            header(
                'Location: roles.php?mensaje=permisos_actualizados'
            );
            exit;

        } catch (PDOException $e) {

            if ($conexion->inTransaction()) {
                $conexion->rollBack();
            }

            error_log($e->getMessage());

            $error = 'No se pudieron actualizar los permisos.';
        }
    }

    // Mantener la selección si hay error
    $asignados = $seleccionados;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Permisos del rol - NutriSalud</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f7f6;
            margin: 30px;
        }

        .contenedor {
            background: white;
            max-width: 600px;
            margin: auto;
            padding: 25px;
            border-radius: 10px;
        }

        h1 {
            color: #24765b;
        }

        .permiso {
            padding: 12px;
            border-bottom: 1px solid #ddd;
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

        .volver {
            display: inline-block;
            margin-top: 20px;
            color: #24765b;
            text-decoration: none;
        }
    </style>

</head>

<body>

<div class="contenedor">

    <h1>Administrar permisos</h1>

    <h3>
        Rol: <?= htmlspecialchars($rol['nombre'], ENT_QUOTES, 'UTF-8') ?>
    </h3>

    <?php if ($error !== ''): ?>
        <p class="error">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <p>Selecciona los permisos que tendrá este rol:</p>

        <?php foreach ($permisos as $permiso): ?>

            <div class="permiso">

                <label>

                    <input
                        type="checkbox"
                        name="permisos[]"
                        value="<?= (int)$permiso['id'] ?>"
                        <?= in_array(
                            (int)$permiso['id'],
                            $asignados,
                            true
                        ) ? 'checked' : '' ?>
                    >

                    <strong>
                        <?= htmlspecialchars(
                            $permiso['nombre'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </strong>

                    <br>

                    <small>
                        <?= htmlspecialchars(
                            $permiso['descripcion'] ?? '',
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </small>

                </label>

            </div>

        <?php endforeach; ?>

        <button type="submit">
            Guardar permisos
        </button>

    </form>

    <a class="volver" href="roles.php">
        ← Volver a roles
    </a>

</div>

</body>
</html>

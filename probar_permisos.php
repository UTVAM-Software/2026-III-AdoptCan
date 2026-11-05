
<?php
require_once __DIR__ . '/config/conexion.php';

$usuario_id = 1;

$sql = "SELECT u.nombre, r.nombre AS rol, p.nombre AS permiso
        FROM usuarios u
        INNER JOIN roles r ON u.rol_id = r.id
        INNER JOIN rol_permiso rp ON r.id = rp.rol_id
        INNER JOIN permisos p ON rp.permiso_id = p.id
        WHERE u.id = ?";

$stmt = $conexion->prepare($sql);
$stmt->execute([$usuario_id]);

$resultados = $stmt->fetchAll();

if ($resultados) {
    echo "<h2>Permisos del usuario</h2>";
    echo "<p>Usuario: " .
         htmlspecialchars($resultados[0]['nombre']) . "</p>";
    echo "<p>Rol: " .
         htmlspecialchars($resultados[0]['rol']) . "</p>";

    echo "<ul>";

    foreach ($resultados as $fila) {
        echo "<li>" .
             htmlspecialchars($fila['permiso']) .
             "</li>";
    }

    echo "</ul>";
} else {
    echo "El usuario no tiene permisos asignados.";
}
?>

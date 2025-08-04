<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT * FROM maestros WHERE activo = 1 ORDER BY nombre";
$maestros = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$mensaje = $_GET['mensaje'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>División ISC</title>
    <link rel="stylesheet" href="assets/division.css">
</head>
<body>

    <!-- ENCABEZADO CON LOGOS -->
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="Logo Izquierdo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="Logo Derecho">
    </header>

    <div class="container">
        <h1>División ISC</h1>

        <a href="index.php" class="btn back">Regresar al Menú</a>
        <a href="agregar_maestro.php" class="btn">Agregar Docente</a>

        <?php if ($mensaje): ?>
            <div class="alert success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    
                    <th>Acciones</th>
                
                </tr>
            </thead>
            <tbody>
                <?php foreach ($maestros as $maestro): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($maestro['nombre']); ?></td>
                        <td>
                            <a href="modificar_maestro.php?id=<?php echo $maestro['id']; ?>" class="btn small">Modificar</a>
                            <a href="eliminar_maestro.php?id=<?php echo $maestro['id']; ?>" class="btn small danger"
                               onclick="return confirm('¿Seguro que deseas eliminar este docente?');">
                               Eliminar
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>

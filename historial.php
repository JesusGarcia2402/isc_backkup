<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

// Eliminar historial completo con confirmación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_historial'])) {
    $conn->query("DELETE FROM historial_modificaciones");
    header("Location: historial.php?mensaje=Historial eliminado correctamente");
    exit;
}

// Obtener historial
$sql = "SELECT h.*, a.nombre_completo 
        FROM historial_modificaciones h
        LEFT JOIN alumnos a ON h.alumno_id = a.id
        ORDER BY h.fecha DESC";

$result = $conn->query($sql);

$modificaciones = [];
if ($result && $result->num_rows > 0) {
    $modificaciones = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Modificaciones</title>
    <link rel="stylesheet" href="assets/historial.css">
    <style>
        .mensaje-flotante {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            text-align: center;
            margin-bottom: 15px;
            animation: desaparecer 4s forwards;
        }
        @keyframes desaparecer {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }

        .btn-danger {
            background-color: #c62828;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .btn-danger:hover {
            background-color: #b71c1c;
        }
    </style>
</head>
<body>
    <!-- Encabezado -->
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="TESJo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="TecNM">
    </header>

    <div class="container">
        <h1>Historial de Modificaciones</h1>

        <a href="index.php" class="btn back">← Regresar al Menú</a>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="mensaje-flotante"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar todo el historial?');">
            <button type="submit" name="eliminar_historial" class="btn-danger">🗑 Eliminar Todo el Historial</button>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Alumno</th>
                    <th>Paso</th>
                    <th>Acción</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($modificaciones)): ?>
                    <?php foreach ($modificaciones as $mod): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($mod['nombre_completo'] ?? 'Desconocido'); ?></td>
                            <td><?php echo htmlspecialchars($mod['paso']); ?></td>
                            <td><?php echo htmlspecialchars($mod['accion']); ?></td>
                            <td><?php echo htmlspecialchars($mod['usuario']); ?></td>
                            <td><?php echo htmlspecialchars($mod['fecha']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="alert info">No hay registros de modificaciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

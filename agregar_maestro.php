<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);

    if (!empty($nombre)) {
        $stmt = $conn->prepare("INSERT INTO maestros (nombre, activo) VALUES (?, 1)");
        $stmt->bind_param("s", $nombre);
        if ($stmt->execute()) {
            $message = 'Maestro agregado correctamente';
            $messageType = 'success';
        } else {
            $message = 'Error al agregar el maestro';
            $messageType = 'error';
        }
    } else {
        $message = 'El nombre no puede estar vacío';
        $messageType = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Maestro</title>
    <link rel="stylesheet" href="assets/agregar_maestro.css">
    <style>
        .mensaje-flotante {
            position: fixed;
            top: 150px;
            right:825px;
            padding: 12px 20px;
            border-radius: 8px;
            color: white;
            z-index: 9999;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            font-size: 16px;
            animation: desaparecer 4s forwards;
        }
        .mensaje-flotante.success {
            background-color: #4CAF50;
        }
        .mensaje-flotante.error {
            background-color: #f44336;
        }
        @keyframes desaparecer {
            0% { opacity: 1; }
            80% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>
</head>
<body>
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="TESJo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="TecNM">
    </header>

    <?php if (!empty($message)): ?>
        <div class="mensaje-flotante <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Agregar Maestro</h1>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del maestro:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <button type="submit" class="btn">Guardar</button>
            <a href="division.php" class="btn back">Regresar</a>
        </form>
    </div>
</body>
</html>

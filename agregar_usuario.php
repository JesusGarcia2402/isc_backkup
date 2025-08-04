<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'Juan Carlos Ambriz Polo') {
    header("Location: index.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['usuario']);
    $nueva_contrasena = $_POST['contrasena'];

    if (!empty($nuevo_usuario) && !empty($nueva_contrasena)) {
        $sql = "INSERT INTO usuarios (usuario, contrasena) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nuevo_usuario, $nueva_contrasena);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $mensaje = '<div class="alert error">Error al agregar el usuario.</div>';
        }
    } else {
        $mensaje = '<div class="alert error">Todos los campos son obligatorios.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Usuario</title>
    <link rel="stylesheet" href="assets/agregar_usuario.css">
</head>
<body>
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="TESJo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="TecNM">
    </header>

    <div class="container">
        <h1>Agregar Nuevo Usuario</h1>
        <?php echo $mensaje; ?>
        <form method="POST" action="agregar_usuario.php">
            <div class="form-group">
                <label for="usuario">Nombre de Usuario:</label>
                <input type="text" name="usuario" id="usuario" required>
            </div>
            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="text" name="contrasena" id="contrasena" required>
            </div>
            <button type="submit" class="btn">Agregar Usuario</button>
            <a href="index.php" class="btn back">Cancelar</a>
        </form>
    </div>
</body>
</html>

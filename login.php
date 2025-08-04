<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM usuarios WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario_db = $result->fetch_assoc();

    if ($usuario_db && $contrasena === $usuario_db['contrasena']) {
        $_SESSION['usuario'] = $usuario_db['usuario'];
        header('Location: index.php');
        exit;
    } else {
        $message = '<div class="alert error">Usuario o contraseña incorrectos</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/login.css">
</head>
<body>

    <!-- ENCABEZADO BONITO -->
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="Logo Izquierdo">
        <div class="titulo">
            Tecnológico de Estudios Superiores de Jocotitlán
        </div>
        <img src="imagenes/R.png" alt="Logo Derecho">
    </header>

    <!-- FONDO INFERIOR -->
    <div class="login-wrapper">
        <div class="container">
            <h1>Iniciar Sesión</h1>
            <?php echo $message; ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="usuario">Usuario Del Dueño de Esto:</label>
                    <input type="text" name="usuario" id="usuario" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" name="contrasena" id="contrasena" required>
                </div>
                <button type="submit" class="btn">Ingresar</button>
            </form>
        </div>
    </div>

</body>
</html>

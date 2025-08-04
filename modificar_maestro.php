<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

$id = $_GET['id'] ?? null;
$message = '';

if ($id) {
    $stmt = $conn->prepare("SELECT * FROM maestros WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $maestro = $stmt->get_result()->fetch_assoc();

    if (!$maestro) {
        $message = '<div class="alert error">Maestro no encontrado</div>';
    }
} else {
    $message = '<div class="alert error">ID inválido</div>';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    if (!empty($nombre)) {
        $stmt = $conn->prepare("UPDATE maestros SET nombre = ? WHERE id = ?");
        $stmt->bind_param("si", $nombre, $id);
        if ($stmt->execute()) {
            $message = '<div class="alert success">Maestro actualizado correctamente</div>';
        } else {
            $message = '<div class="alert error">Error al actualizar</div>';
        }
    } else {
        $message = '<div class="alert error">El nombre no puede estar vacío</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Modificar Maestro</title>
    <link rel="stylesheet" href="assets/modificar_maestro.css">
</head>
<body>
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="TESJo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="TecNM">
    </header>

    <div class="container">
        <h1>Modificar Maestro</h1>
        <?php echo $message; ?>

        <?php if (isset($maestro)): ?>
        <form method="POST">
            <div class="form-group">
                <label for="nombre">Nombre del maestro:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($maestro['nombre']); ?>" required>
            </div>
            <button type="submit" class="btn">Guardar Cambios</button>
            <a href="division.php" class="btn back">Regresar</a>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>

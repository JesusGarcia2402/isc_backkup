<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if (!isset($_GET['id'])) {
    header("Location: consultar.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$alumno_id = intval($_GET['id']);

// Primero eliminar el proceso de titulación si existe (evitar conflicto de clave foránea)
$conn->query("DELETE FROM proceso_titulacion WHERE alumno_id = $alumno_id");

// Luego eliminar al alumno
$conn->query("DELETE FROM alumnos WHERE id = $alumno_id");

header("Location: consultar.php");
exit;

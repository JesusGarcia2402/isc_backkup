<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$id = intval($_GET['id'] ?? 0);

$db = new Database();
$conn = $db->getConnection();

// Eliminar definitivamente
$sql = "DELETE FROM maestros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: division.php?mensaje=Docente eliminado permanentemente");
exit;

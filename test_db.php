<?php
require_once 'includes/config.php';  // Asegúrate de tener las constantes definidas
require_once 'includes/db.php';      // Incluye la clase Database

$db = new Database();                // Crea instancia
$conn = $db->getConnection();        // Obtiene conexión

// Ejemplo: consulta de prueba
$sql = "SELECT * FROM alumnos LIMIT 5";
$resultados = $db->query($sql);

if ($resultados) {
    echo "<h3>Consulta exitosa:</h3><pre>";
    print_r($resultados);
    echo "</pre>";
} else {
    echo "No se encontraron resultados o ocurrió un error.";
}

$db->closeConnection(); // Cierra conexión
?>

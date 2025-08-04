<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'isc';
$fecha = date('Y-m-d_H-i-s');
$nombreArchivo = "respaldo_{$dbname}_{$fecha}.sql";
$carpeta = __DIR__ . "/respaldos";
$rutaArchivo = "$carpeta/$nombreArchivo";

if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Ruta absoluta al mysqldump de XAMPP
$mysqldump = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

$comando = "\"$mysqldump\" --user=$user $dbname > \"$rutaArchivo\"";
if (!empty($pass)) {
    $comando = "\"$mysqldump\" --user=$user --password=$pass $dbname > \"$rutaArchivo\"";
}

system($comando, $resultado);
file_put_contents("debug_dump.txt", "Resultado: $resultado\nComando: $comando\nArchivo: $rutaArchivo");

if ($resultado === 0 && file_exists($rutaArchivo)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/sql');
    header('Content-Disposition: attachment; filename="' . basename($rutaArchivo) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($rutaArchivo));
    readfile($rutaArchivo);
    exit;
} else {
    echo "❌ Error al generar el respaldo.<br>Revisa debug_dump.txt.";
}

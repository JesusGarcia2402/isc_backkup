<?php
require 'vendor/autoload.php';
use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

if (!isset($_FILES['archivo_pdf'])) {
    echo json_encode(['error' => 'No se subió ningún archivo']);
    exit();
}

$archivo = $_FILES['archivo_pdf']['tmp_name'];

if (!file_exists($archivo) || filesize($archivo) === 0) {
    echo json_encode(['error' => 'Archivo no válido o vacío']);
    exit();
}

$parser = new Parser();

try {
    $pdf = $parser->parseFile($archivo);
    $texto = $pdf->getText();
    $texto = preg_replace('/\s+/', ' ', $texto); // Elimina saltos de línea/múltiples espacios
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Error al leer el PDF',
        'detalle' => $e->getMessage()
    ]);
    exit();
}

// --------------------
// Buscar ASESOR
// --------------------
$asesor = '';
if (preg_match('/asesor al\s+(.*?)\s+(y como|quienes|quiénes|que le han sido|quien le ha sido)/i', $texto, $match)) {

    $asesor = trim($match[1]);
}

// --------------------
// Buscar REVISOR 1
// --------------------
$revisor1 = '';
if (preg_match('/Revisor\s*1:\s*(.*?)\s+Revisor\s*2:/i', $texto, $match)) {
    $revisor1 = trim($match[1]);
}

// --------------------
// Buscar REVISOR 2
// --------------------
$revisor2 = '';
if (preg_match('/Revisor\s*2:\s*(.*?)\s+Revisor\s*3:/i', $texto, $match)) {
    $revisor2 = trim($match[1]);
}

// --------------------
// Buscar REVISOR 3
// --------------------
$revisor3 = '';
if (preg_match('/Revisor\s*3:\s*(.*?)(Quiénes|Sin otro|$)/i', $texto, $match)) {
    $revisor3 = trim($match[1]);
}

// --------------------
// RESPUESTA
// --------------------
echo json_encode([
    'asesor'   => $asesor,
    'revisor1' => $revisor1,
    'revisor2' => $revisor2,
    'revisor3' => $revisor3
]);
exit();

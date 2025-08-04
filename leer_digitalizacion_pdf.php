<?php
require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

function extraerDatosDigitalizacionDesdePDF($file_path) {
    $parser = new Parser();
    $pdf = $parser->parseFile($file_path);
    $text = $pdf->getText();

    $datos = [
        'numero_registro' => '',
        'numero_cds' => ''
    ];

    // Buscar número de registro (ej. V0623 o V-0623)
    if (preg_match('/(V[-\s]?\d{3,4})/i', $text, $match)) {
        $datos['numero_registro'] = strtoupper(str_replace([' ', '-'], '', $match[1]));
    }

    // Buscar número de CDs (ej. 2 CDs o 2 CD's o dos CD)
    if (preg_match('/(\d+)\s*CD[\'s]*/i', $text, $match)) {
        $datos['numero_cds'] = $match[1];
    }

    return $datos;
}

// Si es POST desde fetch()
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_pdf'])) {
    $tmp_path = $_FILES['archivo_pdf']['tmp_name'];
    
    if (!file_exists($tmp_path)) {
        echo json_encode(['error' => 'Archivo no encontrado']);
        exit;
    }

    try {
        $datos = extraerDatosDigitalizacionDesdePDF($tmp_path);
        echo json_encode($datos);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al procesar el PDF: ' . $e->getMessage()]);
    }
}

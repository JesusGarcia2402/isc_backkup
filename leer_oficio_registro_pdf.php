<?php
require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

function extraerNumeroRegistroDesdePDF($filePath) {
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();

    // Busca el número de registro, ejemplo: V0654 o R-2023-001
   if (preg_match('/\b([VRI]-?\d{3,})\b/i', $text, $matches)) {

        return $matches[1];
    }

    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_pdf'])) {
    $tmpPath = $_FILES['archivo_pdf']['tmp_name'];
    $numero = extraerNumeroRegistroDesdePDF($tmpPath);
    
    if ($numero) {
        echo json_encode(['numero_registro' => $numero]);
    } else {
        echo json_encode(['error' => 'No se encontró el número de registro en el PDF.']);
    }
}

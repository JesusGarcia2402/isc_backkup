<?php
require 'vendor/autoload.php'; // Si usas Composer y `smalot/pdfparser`

use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

if (!isset($_FILES['archivo_pdf'])) {
    echo json_encode(['error' => 'No se recibió el archivo.']);
    exit;
}

$archivo = $_FILES['archivo_pdf']['tmp_name'];

try {
    $parser = new Parser();
    $pdf = $parser->parseFile($archivo);
    $texto = $pdf->getText();
    $texto = preg_replace('/\s+/', ' ', $texto); // Limpieza

    // Extraer TÍTULO (después de “título es” o “título:” o similar)
   $titulo = '';
if (preg_match('/t[ií]tulo.*?[“"]([^”"]{10,})[”"]/iu', $texto, $match)) {
    $titulo = trim($match[1]);
}



    // Extraer NÚMERO DE REGISTRO (después de “número de registro”)
    $numero = '';
    if (preg_match('/número de registro\s*([A-Z]?\d{3,})/i', $texto, $match)) {
        $numero = trim($match[1]);
    }

    echo json_encode([
        'titulo' => $titulo,
        'numero_registro' => $numero
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Error al leer el PDF: ' . $e->getMessage()]);
}
file_put_contents('debug_protocolo.txt', $texto);



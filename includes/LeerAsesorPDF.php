<?php
use Smalot\PdfParser\Parser;

function leerAsesorYRevisoresDesdePDF($ruta_pdf) {
    $parser = new Parser();
    $pdf = $parser->parseFile($ruta_pdf);
    $texto = preg_replace('/\s+/', ' ', $pdf->getText());

    $asesor = '';
    if (preg_match('/asesor al\s+(.*?)\s+y como miembros/i', $texto, $match)) {
        $asesor = trim($match[1]);
    }

    $revisor1 = '';
    if (preg_match('/Revisor\s*1:\s*(.*?)\s+Revisor\s*2:/i', $texto, $match)) {
        $revisor1 = trim($match[1]);
    }

    $revisor2 = '';
    if (preg_match('/Revisor\s*2:\s*(.*?)\s+Revisor\s*3:/i', $texto, $match)) {
        $revisor2 = trim($match[1]);
    }

    $revisor3 = '';
    if (preg_match('/Revisor\s*3:\s*(.*?)(Quiénes|Sin otro|$)/i', $texto, $match)) {
        $revisor3 = trim($match[1]);
    }

    return [
        'asesor' => $asesor,
        'revisores' => [$revisor1, $revisor2, $revisor3]
    ];
}

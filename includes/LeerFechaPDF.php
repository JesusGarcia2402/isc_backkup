<?php
use Smalot\PdfParser\Parser;

function leerFechaTitulacionDesdePDF($ruta_pdf) {
    $parser = new Parser();
    $pdf = $parser->parseFile($ruta_pdf);
    $texto = preg_replace('/\s+/', ' ', $pdf->getText());

    $info = [
        'fecha' => '',
        'hora' => '',
        'presidente' => '',
        'secretario' => '',
        'vocal' => '',
        'suplente' => ''
    ];

    if (preg_match('/titulación.*?el día (\d{1,2}\/\d{1,2}\/\d{4})/i', $texto, $m)) {
        $info['fecha'] = trim($m[1]);
    }

    if (preg_match('/a las (\d{1,2}:\d{2})/i', $texto, $m)) {
        $info['hora'] = trim($m[1]);
    }

    if (preg_match('/Presidente:\s*(.*?)\s+Secretario:/i', $texto, $m)) {
        $info['presidente'] = trim($m[1]);
    }

    if (preg_match('/Secretario:\s*(.*?)\s+Vocal:/i', $texto, $m)) {
        $info['secretario'] = trim($m[1]);
    }

    if (preg_match('/Vocal:\s*(.*?)\s+Suplente:/i', $texto, $m)) {
        $info['vocal'] = trim($m[1]);
    }

    if (preg_match('/Suplente:\s*(.*?)(Sin otro|Atentamente|$)/i', $texto, $m)) {
        $info['suplente'] = trim($m[1]);
    }

    return $info;
}

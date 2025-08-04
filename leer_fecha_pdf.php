<?php
require_once 'vendor/autoload.php';

use Smalot\PdfParser\Parser;

function parseFechaEnTexto($textoFecha) {
    $meses = [
        'enero' => '01', 'febrero' => '02', 'marzo' => '03',
        'abril' => '04', 'mayo' => '05', 'junio' => '06',
        'julio' => '07', 'agosto' => '08', 'septiembre' => '09',
        'octubre' => '10', 'noviembre' => '11', 'diciembre' => '12'
    ];

    if (preg_match('/(\d{1,2}) de (\w+) de (\d{4})/iu', $textoFecha, $m)) {
        $dia = str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $mes = strtolower($m[2]);
        $anio = $m[3];
        if (isset($meses[$mes])) {
            return "$anio-{$meses[$mes]}-$dia";
        }
    }

    return null; // Si no se puede convertir
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo_pdf'])) {
    $file = $_FILES['archivo_pdf']['tmp_name'];

    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($file);
        $texto = preg_replace('/\s+/', ' ', $pdf->getText());

        // Extraer fecha textual y convertirla
        $fecha = '';
        if (preg_match('/(?:el|para el)\s+(\d{1,2} de \w+ de \d{4})/iu', $texto, $match_fecha)) {
            $fecha = parseFechaEnTexto($match_fecha[1]);
        }

        // Extraer hora (ej. "13:00")
        $hora = '';
        if (preg_match('/a las (\d{1,2}:\d{2})/i', $texto, $match_hora)) {
            $hora = $match_hora[1] . ':00';
        }

        // Extraer miembros del jurado
        function extraerDocente($cargo, $texto) {
            if (preg_match("/$cargo\s+(.*?)\s+\d{6,}/i", $texto, $m)) {
                return trim($m[1]);
            }
            return '';
        }

        $presidente = extraerDocente('Presidente', $texto);
        $secretario = extraerDocente('Secretario', $texto);
        $vocal = extraerDocente('Vocal', $texto);
        $suplente = extraerDocente('Suplente', $texto);

        echo json_encode([
            'fecha' => $fecha,
            'hora' => $hora,
            'presidente' => $presidente,
            'secretario' => $secretario,
            'vocal' => $vocal,
            'suplente' => $suplente
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al leer el PDF: ' . $e->getMessage()]);
        exit;
    }
}

echo json_encode(['error' => 'No se pudo procesar el archivo']);
<?php
require('fpdf/fpdf.php'); // asegúrate de tener la librería FPDF

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descargar_oficio'])) {
    $fecha = $_POST['fecha_oficio'];
    $numero = $_POST['numero_oficio'];
    $alumno = $_POST['nombre_estudiante'];
    $proyecto = $_POST['nombre_proyecto'];
    $opcion = $_POST['opcion_titulacion'];
    $modalidad = $_POST['modalidad'] ?? '';

    $asesor = $_POST['asesor'][0];
    $revisores = $_POST['revisor'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    $pdf->Cell(0, 10, utf8_decode("$fecha"), 0, 1, 'R');
    $pdf->Cell(0, 10, utf8_decode("$numero"), 0, 1, 'R');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->MultiCell(0, 8, utf8_decode("C. $alumno\nPASANTE DE LA LICENCIATURA DE INGENIERÍA\nEN SISTEMAS COMPUTACIONALES\nPRESENTE."), 0, 'L');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 12);
    $pdf->MultiCell(0, 8, utf8_decode("Sirva el presente para comunicarle que ante la revisión realizada por la Academia de Ingeniería en Sistemas Computacionales a su anteproyecto denominado “$proyecto”, por la opción $opcion, ha sido ACEPTADO siempre y cuando se atiendan las observaciones sugeridas en su trabajo escrito."), 0, 'J');
    $pdf->Ln(5);

    $pdf->MultiCell(0, 8, utf8_decode("De igual manera le comunico que le ha sido designado como asesor el $asesor y como miembros de la comisión revisora a:\n\nRevisor 1: $revisores[0]\nRevisor 2: $revisores[1]\nRevisor 3: $revisores[2]\n\nQuiénes le orientarán y asesorarán en el desarrollo de su trabajo escrito hasta su terminación."), 0, 'J');
    $pdf->Ln(15);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, utf8_decode("ATENTAMENTE"), 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, utf8_decode("M. en C. C. JUAN CARLOS AMBRIZ POLO"), 0, 1, 'C');
    $pdf->Cell(0, 10, utf8_decode("JEFE DE LA DIVISION DE INGENIERIA"), 0, 1, 'C');
    $pdf->Cell(0, 10, utf8_decode("EN SISTEMAS COMPUTACIONALES"), 0, 1, 'C');

    $pdf->Output('D', 'Oficio_Asesor.pdf');
}
?>

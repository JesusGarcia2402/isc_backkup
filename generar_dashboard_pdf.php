<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'fpdf/fpdf.php';

$db = new Database();
$conn = $db->getConnection();
$pdf = new FPDF();
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('Reporte General de Titulación'), 0, 1, 'C');
$pdf->Ln(5);

// === Total Titulados ===
$sql_total = "SELECT SUM(cantidad) AS total FROM titulados_anio";
$res_total = $conn->query($sql_total);
$total = $res_total->fetch_assoc()['total'] ?? 0;

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, "Total Titulados: $total", 0, 1);

// === Estados ===
$estados = [
    'pendiente' => 0,
    'en-progreso' => 0,
    'en-titulacion' => 0,
];

$sql_estados = "SELECT 
    SUM(CASE WHEN protocolo_status = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
    SUM(CASE WHEN protocolo_status = 'en-progreso' THEN 1 ELSE 0 END) AS en_progreso,
    SUM(CASE WHEN protocolo_status = 'completado' THEN 1 ELSE 0 END) AS en_titulacion
    FROM proceso_titulacion";

$res_estados = $conn->query($sql_estados);
if ($row = $res_estados->fetch_assoc()) {
    $estados['pendiente'] = $row['pendientes'];
    $estados['en-progreso'] = $row['en_progreso'];
    $estados['en-titulacion'] = $row['en_titulacion'];
}

$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Estado de Proceso:', 0, 1);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 8, "Pendientes: " . $estados['pendiente'], 0, 1);
$pdf->Cell(0, 8, utf8_decode("En Progreso: ") . $estados['en-progreso'], 0, 1);
$pdf->Cell(0, 8, utf8_decode("En Titulación: ") . $estados['en-titulacion'], 0, 1);

// === Últimos Titulados ===
$sql_ultimos = "SELECT a.nombre_completo, a.numero_control, p.fecha_titulacion_fecha
                FROM alumnos a
                JOIN proceso_titulacion p ON a.id = p.alumno_id
                WHERE p.fecha_titulacion_fecha IS NOT NULL
                ORDER BY p.fecha_titulacion_fecha DESC
                LIMIT 5";

$res_ultimos = $conn->query($sql_ultimos);
$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Últimos Titulados:'), 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Nombre', 1);
$pdf->Cell(50, 8, 'Numero De Control', 1);
$pdf->Cell(40, 8, 'Fecha', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
while ($row = $res_ultimos->fetch_assoc()) {
    $pdf->Cell(60, 8, utf8_decode($row['nombre_completo']), 1);
    $pdf->Cell(50, 8, $row['numero_control'], 1);
    $pdf->Cell(40, 8, $row['fecha_titulacion_fecha'], 1);
    $pdf->Ln();
}

// Descargar
$pdf->Output('D', 'dashboard_reporte.pdf');
exit;

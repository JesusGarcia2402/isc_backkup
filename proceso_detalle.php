<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'fpdf/fpdf.php';
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/Uploads.php';

use setasign\Fpdi\Fpdi;

  // ESTO ES PARA EL ASESOR

if (isset($_POST['descargar_oficio'])) {

    // Obtener los datos del formulario
    $fecha_oficio = $_POST['fecha_oficio'] ?? '';
    $numero_oficio   = $_POST['numero_oficio'] ?? '';
    $nombre   = $_POST['nombre_estudiante'] ?? '';
    $proyecto = $_POST['nombre_proyecto'] ?? '';
    $opcion   = $_POST['opcion_titulacion'] ?? '';
    $asesor   = $_POST['asesores'][0] ?? '';
    $rev1     = $_POST['comision_revisora'][0] ?? '';
    $rev2     = $_POST['comision_revisora'][1] ?? '';
    $rev3     = $_POST['comision_revisora'][2] ?? '';

     $fecha_formateada = fechaEnEspañol($fecha_oficio);

    // Crear el PDF con FPDI
    $pdf = new \setasign\Fpdi\Fpdi();
    $pdf->AddPage();

    // Cargar la plantilla
    $pdf->setSourceFile(__DIR__ . '/plantillas/LOGOS.pdf');
    $tplIdx = $pdf->importPage(1);
    $pdf->useTemplate($tplIdx);

    // Configuración de fuente y color
    $pdf->SetFont('Arial', '', 8);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetMargins(25, 30, 25);

    // Fecha y número de oficio (esquina superior derecha)
    $pdf->SetXY(25, 45);
    $pdf->SetFont('Arial', 'B', 10.5);
    $pdf->MultiCell(0, 6, utf8_decode("Jocotitlán, Estado de México, a $fecha_formateada\nOficio No. $numero_oficio"), 0, 'R');
    

    // Espacio vertical
    $pdf->Ln(8);

    // Destinatario
    $pdf->SetFont('Arial', 'B', 11.5);
    $pdf->MultiCell(0, 6.5, utf8_decode("C.  $nombre\nPASANTE DE LA LICENCIATURA DE INGENIERÍA\nEN SISTEMAS COMPUTACIONALES\nPRESENTE."), 0, 'L');

    $pdf->Ln(6);

    // Cuerpo principal (todo como un solo párrafo justificado)
    $pdf->SetFont('Arial', '', 11);
    $texto = "Sirva el presente para comunicarle que ante la revisión realizada por la Academia de Ingeniería en Sistemas Computacionales a su anteproyecto denominado " . strtoupper($proyecto) . ", por la opción  " . $opcion . ", ha sido ACEPTADO siempre y cuando se atiendan las observaciones sugeridas en su trabajo escrito.";
    $pdf->MultiCell(0, 6, utf8_decode($texto), 0, 'J');

    $pdf->Ln(6);

    // Comité asignado
    $texto2 = "De igual manera le comunico que le ha sido designado como asesor al $asesor y como miembros de la comisión revisora a:";
    $pdf->MultiCell(0, 6, utf8_decode($texto2), 0, 'J');

    // Revisores
    $pdf->Ln(3);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->MultiCell(0, 6, utf8_decode("Revisor 1: $rev1\nRevisor 2: $rev2\nRevisor 3: $rev3"), 0, 'L');

    // Cierre
    $pdf->Ln(4);
    $pdf->SetFont('Arial', '', 11);
    $pdf->MultiCell(0, 6, utf8_decode("Quiénes le orientarán y asesorarán en el desarrollo de su trabajo escrito hasta su terminación."), 0, 'J');

    $pdf->Ln(10);
    $pdf->MultiCell(0, 6, utf8_decode("Sin otro particular quedo de usted, para cualquier duda o aclaración."), 0, 'C');

    // Firma
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode("ATENTAMENTE"), 0, 1, 'C');
    $pdf->Ln(8);
    $pdf->Cell(0, 6, utf8_decode("M. en C. C. JUAN CARLOS AMBRIZ POLO"), 0, 1, 'C');
    $pdf->Cell(0, 6, utf8_decode("JEFE DE LA DIVISIÓN DE INGENIERÍA"), 0, 1, 'C');
    $pdf->Cell(0, 6, utf8_decode("EN SISTEMAS COMPUTACIONALES"), 0, 1, 'C');



    // Descargar el PDF
    ob_end_clean(); // importante
    $pdf->Output('D', 'Oficio_Asesor.pdf');
    exit;
}

  // ESTO ES PARA TITULACION

function fechaEnEspañol($fecha) {
    $meses = [
        'January' => 'enero', 'February' => 'febrero', 'March' => 'marzo',
        'April' => 'abril', 'May' => 'mayo', 'June' => 'junio',
        'July' => 'julio', 'August' => 'agosto', 'September' => 'septiembre',
        'October' => 'octubre', 'November' => 'noviembre', 'December' => 'diciembre'
    ];

       // Si la fecha es en formato DD/MM/YYYY, conviértela a YYYY-MM-DD
    if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $fecha, $matches)) {
        $fecha = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
    }
    
    $timestamp = strtotime($fecha);
    if ($timestamp === false) {
        return "Fecha inválida"; // Manejo de error por si acaso
    }

    $timestamp = strtotime($fecha);
    $dia = date('j', $timestamp);
    $mesIngles = date('F', $timestamp);
    $anio = date('Y', $timestamp);
    return "$dia de " . $meses[$mesIngles] . " de $anio";
}

if (isset($_POST['descargar_oficio_fecha'])) {
    $fecha_oficio = $_POST['fecha_oficio'] ?? '';
    $numero_oficio = $_POST['numero_oficio'] ?? '';
    $nombre = strtoupper($_POST['nombre_estudiante'] ?? '');
    $control = strtoupper($_POST['numero_control'] ?? '');
    $opcion = strtoupper($_POST['opcion_titulacion'] ?? '');
    $fecha_titulacion = $_POST['fecha_titulacion'] ?? '';
    $hora_titulacion = $_POST['hora_titulacion'] ?? '';
    $hora_formateada = date('H:i', strtotime($hora_titulacion));
    $presidente = $_POST['presidente'] ?? '';
    $secretario = $_POST['secretario'] ?? '';
    $vocal = $_POST['vocal'] ?? '';
    $suplente = $_POST['suplente'] ?? '';
    $cedula_presidente = $_POST['cedula_presidente'] ?? '';
    $cedula_secretario = $_POST['cedula_secretario'] ?? '';
    $cedula_vocal = $_POST['cedula_vocal'] ?? '';
    $cedula_suplente = $_POST['cedula_suplente'] ?? '';
    
   
 $fecha_formateada = fechaEnEspañol($fecha_oficio);
$fecha_titulacion_formateada = fechaEnEspañol($fecha_titulacion);     // ✅ Usando solo tu función personalizada


    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->setSourceFile(__DIR__ . '/plantillas/LOGOS.pdf');
    $tpl = $pdf->importPage(1);
    $pdf->useTemplate($tpl);

    $pdf->SetMargins(25, 40, 25);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 11);

    // Fecha y número de oficio
    $pdf->SetXY(25, 45);
    $pdf->SetFont('Arial', 'B', 10.5);
   $pdf->MultiCell(0, 6, utf8_decode("Jocotitlán, Estado de México, a $fecha_formateada\nOficio No. $numero_oficio"), 0, 'R');

    // Encabezado
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->MultiCell(0, 6, utf8_decode("LIC. JONATHAN ARMANDO CARREÓN APONTE\nJEFE DEL DEPARTAMENTO DE TITULACIÓN\nPRESENTE"), 0, 'L');

    // Cuerpo
    $pdf->Ln(6);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode("Muy Distinguido Licenciado:"), 0, 1);

    $pdf->Ln(3);
    $pdf->SetFont('Arial', '', 11);
    $cuerpo = "En atención a que el (la) C. $nombre, pasante de la Licenciatura en Ingeniería en Sistemas Computacionales, con número de control $control reúne los requisitos para titularse por la opción $opcion, y en cumplimiento con el artículo 25 del Reglamento de Titulación, me permito asignar el siguiente jurado para el $fecha_titulacion_formateada a las $hora_formateada horas.";
    $pdf->MultiCell(0, 6, utf8_decode($cuerpo), 0, 'J');

    // Tabla más compacta y centrada
    $xTabla = 25;
    $pdf->Ln(8);
    $pdf->SetX($xTabla);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(40, 8, utf8_decode("Cargo"), 1, 0, 'C');
    $pdf->Cell(85, 8, utf8_decode("Nombre del Docente"), 1, 0, 'C');
    $pdf->Cell(40, 8, utf8_decode("No. Cédula"), 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);
    $pdf->SetX($xTabla);
    $pdf->Cell(40, 8, utf8_decode("Presidente"), 1, 0);
    $pdf->Cell(85, 8, utf8_decode($presidente), 1, 0);
    $pdf->Cell(40, 8, utf8_decode($cedula_presidente), 1, 1);

    $pdf->SetX($xTabla);
    $pdf->Cell(40, 8, utf8_decode("Secretario"), 1, 0);
    $pdf->Cell(85, 8, utf8_decode($secretario), 1, 0);
    $pdf->Cell(40, 8, utf8_decode($cedula_secretario), 1, 1);

    $pdf->SetX($xTabla);
    $pdf->Cell(40, 8, utf8_decode("Vocal"), 1, 0);
    $pdf->Cell(85, 8, utf8_decode($vocal), 1, 0);
    $pdf->Cell(40, 8, utf8_decode($cedula_vocal), 1, 1);

    $pdf->SetX($xTabla);
    $pdf->Cell(40, 8, utf8_decode("Suplente"), 1, 0);
    $pdf->Cell(85, 8, utf8_decode($suplente), 1, 0);
    $pdf->Cell(40, 8, utf8_decode($cedula_suplente), 1, 1);

// Cierre
$pdf->Ln(8);
$pdf->MultiCell(0, 6, utf8_decode("Sin más por el momento, quedo de usted para cualquier duda o aclaración."), 0, 'J');

// ATENTAMENTE y Vo. Bo. en misma línea
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(95, 6, utf8_decode("ATENTAMENTE"), 0, 0, 'L');
$pdf->Cell(95, 6, utf8_decode("Vo. Bo."), 0, 1, 'L');

// Firmas en paralelo sin alineación derecha
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 9.5);
$pdf->Cell(95, 6, utf8_decode("M. EN C. C. JUAN CARLOS AMBRIZ POLO"), 0, 0, 'L');
$pdf->Cell(95, 6, utf8_decode("DRA. EN C. ELIZABETH GARCÍA ALCANTARA"), 0, 1, 'L');

$pdf->Cell(95, 6, utf8_decode("JEFE DE DIVISIÓN DE INGENIERÍA"), 0, 0, 'L');
$pdf->Cell(95, 6, utf8_decode("SUBDIRECTORA DE ESTUDIOS PROFESIONALES"), 0, 1, 'L');

$pdf->Cell(95, 6, utf8_decode("EN SISTEMAS COMPUTACIONALES"), 0, 0, 'L');


   

    ob_end_clean();
    $pdf->Output('D', 'Oficio_Titulacion.pdf');
    exit;
}


  // ESTO ES PARA DIGITALIZACION

 if (isset($_POST['descargar_oficio_digitalizacion'])) {
 

  

    // DATOS
    $fecha_oficio = $_POST['fecha_oficio'] ?? '';
    $numero_oficio = $_POST['numero_oficio'] ?? '';
    $nombre = strtoupper($_POST['nombre_estudiante'] ?? '');
    $proyecto = strtoupper($_POST['nombre_proyecto'] ?? '');
    $registro = strtoupper($_POST['numero_registro'] ?? '');
    $opcion = strtoupper($_POST['opcion_titulacion'] ?? '');
    $cds = $_POST['numero_cds'] ?? '0';

    $fecha_formateada = fechaEnEspañol($fecha_oficio);

    // PDF
    $pdf = new Fpdi();
    $pdf->AddPage();
    $pdf->setSourceFile(__DIR__ . '/plantillas/LOGOS.pdf');
    $tpl = $pdf->importPage(1);
    $pdf->useTemplate($tpl);

    $pdf->SetMargins(25, 40, 25);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 11);

    // Fecha y número de oficio
    $pdf->SetXY(25, 45);
    $pdf->SetFont('Arial', 'B', 10.5);
    $pdf->MultiCell(0, 6, utf8_decode("Jocotitlán, Estado de México, a $fecha_formateada\nOficio No. $numero_oficio"), 0, 'R');

    // Encabezado
    $pdf->Ln(8);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->MultiCell(0, 6, utf8_decode("C. $nombre\nPASANTE DE LA LICENCIATURA DE INGENIERÍA\nEN SISTEMAS COMPUTACIONALES\nPRESENTE."), 0, 'L');

    // Cuerpo
    $pdf->Ln(6);
    $pdf->SetFont('Arial', '', 11);
    $texto = "Sirva el presente para informar a usted, que de acuerdo con la revisión que se hizo al trabajo presentado cuyo título es ";
    $texto .= "$proyecto, con número de registro $registro, correspondiente a la opción $opcion, se le autoriza la DIGITALIZACIÓN de $cds CD´s, los cuales deberán ser entregados al Departamento de Titulación.";
    $pdf->MultiCell(0, 6, utf8_decode($texto), 0, 'J');

    $pdf->Ln(6);
    $pdf->MultiCell(0, 6, utf8_decode("Sin otro particular quedo de usted, para cualquier duda o aclaración."), 0, 'J');

    // Firma
    $pdf->Ln(15);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(0, 6, utf8_decode("ATENTAMENTE"), 0, 1, 'C');

    $pdf->Ln(12);
    $pdf->SetFont('Arial', 'B', 9.5);
    $pdf->Cell(0, 6, utf8_decode("M. en C. C. JUAN CARLOS AMBRIZ POLO"), 0, 1, 'C');
    $pdf->Cell(0, 6, utf8_decode("JEFE DE LA DIVISIÓN DE INGENIERÍA"), 0, 1, 'C');
    $pdf->Cell(0, 6, utf8_decode("EN SISTEMAS COMPUTACIONALES"), 0, 1, 'C');

    ob_end_clean();
    $pdf->Output('D', 'Oficio_Digitalizacion.pdf');
    exit;
}











$db = new Database();
$conn = $db->getConnection();
$uploads = new Uploads();

if (!isset($_GET['alumno_id']) || !isset($_GET['step'])) {
    header("Location: consultar.php");
    exit();
}

$alumno_id = intval($_GET['alumno_id']);
$step = $_GET['step'];

// Validar paso
$valid_steps = [
    'protocolo', 'asesor', 'validacion', 'fecha_titulacion',
    'registro_oficio', 'liberacion', 'numero_registro',
    'oficio_resultados', 'carta_postulacion', 'oficio_aceptacion',
    'asignacion_revisores', 'registro', 'registro_carta_dual',
    'digitalizacion','protocolos'
];


if (!in_array($step, $valid_steps)) {
    header("Location: consultar.php");
    exit();
}

// Obtener datos del alumno
$sql_alumno = "SELECT * FROM alumnos WHERE id = ?";
$stmt_alumno = $conn->prepare($sql_alumno);
$stmt_alumno->bind_param("i", $alumno_id);
$stmt_alumno->execute();
$result_alumno = $stmt_alumno->get_result();
$alumno = $result_alumno->fetch_assoc();

if (!$alumno) {
    header("Location: consultar.php");
    exit();
}

// Obtener proceso de titulación
$sql_proceso = "SELECT * FROM proceso_titulacion WHERE alumno_id = ?";
$stmt_proceso = $conn->prepare($sql_proceso);
$stmt_proceso->bind_param("i", $alumno_id);
$stmt_proceso->execute();
$result_proceso = $stmt_proceso->get_result();
$proceso = $result_proceso->fetch_assoc();
$asesores_guardados = array_map('trim', explode(',', $proceso['asesor_nombres'] ?? ''));
$comision_guardada = array_map('trim', explode(',', $proceso['comision_revisora1'] ?? ''));


$message = '';

// Obtener lista de maestros
$sql_maestros = "SELECT id, nombre FROM maestros WHERE activo = TRUE ORDER BY nombre";
$result_maestros = $conn->query($sql_maestros);

if (!$result_maestros) {
    die("Error al obtener la lista de maestros: " . $conn->error);
}

$maestros = $result_maestros->fetch_all(MYSQLI_ASSOC);

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $status = isset($_POST['finalizar']) ? 'completado' : 'en-progreso';
        
        switch ($step) {
            case 'protocolo':
                $titulo_trabajo = $_POST['titulo_trabajo'];
                $numero_registro = $_POST['numero_registro'];
                
                $file_path = $proceso['protocolo_doc'];
                if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
                    $file_path = $uploads->uploadFile($_FILES['documento'], 'protocolo');
                }
                
                $sql = "UPDATE proceso_titulacion SET 
                        protocolo_status = ?,
                        protocolo_doc = ?,
                        titulo_trabajo = ?,
                        numero_registro = ?
                        WHERE alumno_id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $status, $file_path, $titulo_trabajo, $numero_registro, $alumno_id);
                $stmt->execute();
                
                $sql_alumno = "UPDATE alumnos SET titulo_trabajo = ? WHERE id = ?";
                $stmt_alumno = $conn->prepare($sql_alumno);
                $stmt_alumno->bind_param("si", $titulo_trabajo, $alumno_id);
                $stmt_alumno->execute();


                   
 session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'protocolo'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de protocolo';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();






                
                $message = '<div class="alert success">Datos de protocolo actualizados</div>';
                break;
                
            case 'asesor':
                $asesores = implode(', ', $_POST['asesores']);
                $comision_revisora = implode(', ', $_POST['comision_revisora']);
                
                $file_path = $proceso['asesor_doc'];
                if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
                    $file_path = $uploads->uploadFile($_FILES['documento'], 'asesor');
                }
                
                $sql = "UPDATE proceso_titulacion SET 
                        asesor_status = ?,
                        asesor_doc = ?,
                        asesor_nombres = ?,
                        comision_revisora1 = ?
                        WHERE alumno_id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssi", $status, $file_path, $asesores, $comision_revisora, $alumno_id);
                $stmt->execute();


                
 session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'asesor'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de asesor';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();

                
                $message = '<div class="alert success">Datos de asesor actualizados</div>';
                break;
                
            case 'validacion':
                $file_path = $proceso['validacion_doc'];
                if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
                    $file_path = $uploads->uploadFile($_FILES['documento'], 'validacion');
                }
                
                $sql = "UPDATE proceso_titulacion SET 
                        validacion_status = ?,
                        validacion_doc = ?
                        WHERE alumno_id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $status, $file_path, $alumno_id);
                $stmt->execute();



 session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'validacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de validacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();


                
                $message = '<div class="alert success">Documento de validación actualizado</div>';
                break;
                
            case 'fecha_titulacion':
                $fecha_titulacion = $_POST['fecha_titulacion'];
                $hora_titulacion = $_POST['hora_titulacion'];
                $presidente = $_POST['presidente'];
                $secretario = $_POST['secretario'];
                $vocal = $_POST['vocal'];
                $suplente = $_POST['suplente'] ?? null;
                
                $file_path = $proceso['fecha_titulacion_doc'];
                if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
                    $file_path = $uploads->uploadFile($_FILES['documento'], 'titulacion');
                }
                
                $sql = "UPDATE proceso_titulacion SET 
                        fecha_titulacion_status = ?,
                        fecha_titulacion_doc = ?,
                        presidente = ?,
                        secretario = ?,
                        vocal = ?,
                        suplente = ?,
                        fecha_titulacion_fecha = ?,
                        hora_titulacion = ?
                        WHERE alumno_id = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssssi", $status, $file_path, $presidente, $secretario, $vocal, $suplente, $fecha_titulacion, $hora_titulacion, $alumno_id);
                $stmt->execute();
                
                $message = '<div class="alert success">Datos de fecha de titulación actualizados</div>';
                
                if ($status == 'completado') {
                    $anio = date('Y', strtotime($fecha_titulacion));
                    $sql_estadistica = "INSERT INTO titulados_anio (anio, cantidad) 
                                        VALUES (?, 1) 
                                        ON DUPLICATE KEY UPDATE cantidad = cantidad + 1";
                    $stmt_estadistica = $conn->prepare($sql_estadistica);
                    $stmt_estadistica->bind_param("i", $anio);
                    $stmt_estadistica->execute();
                }





                 session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'fecha_titulacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de fecha_titulacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
                break;


                case 'registro_oficio':
    $numero_registro = $_POST['numero_registro'] ?? '';

    $file_path = $proceso['registro_oficio_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'registro_oficio');
        
        // Detectar número de registro automáticamente si no se envió
        require_once 'leer_oficio_registro_pdf.php';
        $numero_detectado = extraerNumeroRegistroDesdePDF($file_path);
        if ($numero_detectado && empty($numero_registro)) {
            $numero_registro = $numero_detectado;
        }
    }

    $sql = "UPDATE proceso_titulacion SET 
            registro_oficio_status = ?, 
            registro_oficio_doc = ?, 
            numero_registro = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $status, $file_path, $numero_registro, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'registro_oficio'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de registro_oficio';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    $message = '<div class="alert success">Oficio de Registro actualizado correctamente</div>';

    break;

    case 'liberacion':
    $file_path = $proceso['liberacion_doc'];

    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'liberacion');
    }

    $sql = "UPDATE proceso_titulacion SET 
            liberacion_status = ?, 
            liberacion_doc = ?
            WHERE alumno_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $file_path, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'liberacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de liberacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();

    $message = '<div class="alert success">Documento de liberación actualizado correctamente</div>';
    break;

    case 'digitalizacion':
    $numero_registro = $_POST['numero_registro'] ?? '';
    $numero_cds = $_POST['numero_cds'] ?? '';

    $file_path = $proceso['digitalizacion_doc'];

    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'digitalizacion');

        // Extraer automáticamente si no se ingresó manualmente
        require_once 'leer_digitalizacion_pdf.php';
        $datos = extraerDatosDigitalizacionDesdePDF($file_path);
        if (!empty($datos['numero_registro']) && empty($numero_registro)) {
            $numero_registro = $datos['numero_registro'];
        }
        if (!empty($datos['numero_cds']) && empty($numero_cds)) {
            $numero_cds = $datos['numero_cds'];
        }
    }

    $sql = "UPDATE proceso_titulacion SET 
            digitalizacion_status = ?, 
            digitalizacion_doc = ?, 
            numero_registro = ?, 
            numero_cds = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $status, $file_path, $numero_registro, $numero_cds, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'digitalizacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de digitalizacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    $message = '<div class="alert success">Digitalización actualizada correctamente</div>';
    break;

case 'oficio_aceptacion':
    $file_path = $proceso['oficio_aceptacion_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'oficio_aceptacion');
    }

    $sql = "UPDATE proceso_titulacion SET 
            oficio_aceptacion_status = ?, 
            oficio_aceptacion_doc = ? 
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $file_path, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'oficio_aceptacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de oficio_aceptacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;


    case 'numero_registro':
    $numero_registro = $_POST['numero_registro'] ?? '';
    $file_path = $proceso['numero_registro_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'numero_registro');
        require_once 'leer_oficio_registro_pdf.php';
        $detectado = extraerNumeroRegistroDesdePDF($file_path);
        if ($detectado && empty($numero_registro)) {
            $numero_registro = $detectado;
        }
    }

    $sql = "UPDATE proceso_titulacion SET 
            numero_registro_status = ?, 
            numero_registro_doc = ?, 
            numero_registro = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $status, $file_path, $numero_registro, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'numero_registro'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de numero_registro';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;


    case 'asignacion_revisores':
    $asesor = $_POST['asesor'] ?? '';
    $revisor1 = $_POST['revisor1'] ?? '';
    $revisor2 = $_POST['revisor2'] ?? '';
    $revisor3 = $_POST['revisor3'] ?? '';
    $comision = implode(', ', [$revisor1, $revisor2, $revisor3]);

    $file_path = $proceso['asignacion_revisores_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'asignacion_revisores');
        require_once 'leer_asesor_pdf.php';
        $detectados = leerAsesorYRevisoresDesdePDF($file_path);
        $asesor = $detectados['asesor'] ?? $asesor;
        $comision = implode(', ', $detectados['revisores'] ?? [$revisor1, $revisor2, $revisor3]);

    }

    $sql = "UPDATE proceso_titulacion SET 
            asignacion_revisores_status = ?, 
            asignacion_revisores_doc = ?, 
            asesor_nombres = ?, 
            comision_revisora1 = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $status, $file_path, $asesor, $comision, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'asignacion_revisores'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de asignacion_revisores';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;

    case 'liberacion':
    $file_path = $proceso['liberacion_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'liberacion');
    }

    $sql = "UPDATE proceso_titulacion SET 
            liberacion_status = ?, 
            liberacion_doc = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $file_path, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'liberacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de liberacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;

    case 'digitalizacion':
    $numero_registro = $_POST['numero_registro'] ?? '';
    $numero_cds = $_POST['numero_cds'] ?? '';
    $file_path = $proceso['digitalizacion_doc'] ?? '';

    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'digitalizacion');
        require_once 'leer_digitalizacion_pdf.php';
        $datos = extraerDatosDigitalizacionDesdePDF($file_path);
        if ($datos['numero_registro']) $numero_registro = $datos['numero_registro'];
        if ($datos['numero_cds']) $numero_cds = $datos['numero_cds'];
    }

    $sql = "UPDATE proceso_titulacion SET 
            digitalizacion_status = ?, 
            digitalizacion_doc = ?, 
            numero_registro = ?, 
            numero_cds = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $status, $file_path, $numero_registro, $numero_cds, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'digitalizacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de digitalizacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;



case 'carta_postulacion':
    $file_path = $proceso['carta_postulacion_doc'];
    if (isset($_FILES['carta_postulacion_doc']) && $_FILES['carta_postulacion_doc']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['carta_postulacion_doc'], 'carta_postulacion');
    }

    $sql = "UPDATE proceso_titulacion SET
                carta_postulacion_status = ?,
                carta_postulacion_doc = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $estado, $file_path, $alumno_id);
    $estado = 'completado';
    $stmt->execute();

    $message = '<div class="alert success">Carta de postulación guardada correctamente</div>';
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'carta_postulacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de carta_postulacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;


case 'registro_carta_dual':
    $file_path = $proceso['registro_carta_dual_doc'];

    if (isset($_FILES['registro_carta_dual_doc']) && $_FILES['registro_carta_dual_doc']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['registro_carta_dual_doc'], 'registro_carta_dual');
    }

    $sql = "UPDATE proceso_titulacion SET 
                registro_carta_dual_status = ?, 
                registro_carta_dual_doc = ? 
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $estado = 'completado';
    $stmt->bind_param("ssi", $estado, $file_path, $alumno_id);
    $stmt->execute();

    $message = '<div class="alert success">Carta de registro dual guardada correctamente</div>';
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'registro_carta_dual'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de registro_carta_dual';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;




case 'protocolos':
    $file_path1 = $proceso['protocolo_doc1'];
    $file_path2 = $proceso['protocolo_doc2'];
    $file_path3 = $proceso['protocolo_doc3'];

    if (isset($_FILES['protocolo_doc1']) && $_FILES['protocolo_doc1']['error'] === UPLOAD_ERR_OK) {
        $file_path1 = $uploads->uploadFile($_FILES['protocolo_doc1'], 'protocolos');
    }

    if (isset($_FILES['protocolo_doc2']) && $_FILES['protocolo_doc2']['error'] === UPLOAD_ERR_OK) {
        $file_path2 = $uploads->uploadFile($_FILES['protocolo_doc2'], 'protocolos');
    }

    if (isset($_FILES['protocolo_doc3']) && $_FILES['protocolo_doc3']['error'] === UPLOAD_ERR_OK) {
        $file_path3 = $uploads->uploadFile($_FILES['protocolo_doc3'], 'protocolos');
    }

    $sql = "UPDATE proceso_titulacion SET
                protocolos_status = ?,
                protocolo_doc1 = ?,
                protocolo_doc2 = ?,
                protocolo_doc3 = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $estado = 'completado';
    $stmt->bind_param("ssssi", $estado, $file_path1, $file_path2, $file_path3, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'protocolos'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de protocolos';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    $message = '<div class="alert success">Protocolos guardados correctamente</div>';
    break;




    case 'oficio_resultados':
    $file_path = $proceso['oficio_resultados_doc'];
    if (isset($_FILES['oficio_resultados_doc']) && $_FILES['oficio_resultados_doc']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['oficio_resultados_doc'], 'oficio_resultados');
    }

    $sql = "UPDATE proceso_titulacion SET
                oficio_resultados_status = ?,
                oficio_resultados_doc = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $estado, $file_path, $alumno_id);
    $estado = 'completado';
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'oficio_resultados'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de oficio_resultados';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    $message = '<div class="alert success">Oficio de resultados guardado correctamente</div>';
    break;



    case 'registro':
    $file_path = $proceso['registro_doc'];
    if (isset($_FILES['registro_doc']) && $_FILES['registro_doc']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['registro_doc'], 'registro');
    }

    $sql = "UPDATE proceso_titulacion SET
                registro_status = ?,
                registro_doc = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $estado, $file_path, $alumno_id);
    $estado = 'completado';
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'registro'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de registro';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    $message = '<div class="alert success">Registro guardado correctamente</div>';
    break;





    case 'fecha_titulacion':
    $fecha_titulacion = $_POST['fecha_titulacion'];
    $hora_titulacion = $_POST['hora_titulacion'];
    $presidente = $_POST['presidente'];
    $secretario = $_POST['secretario'];
    $vocal = $_POST['vocal'];
    $suplente = $_POST['suplente'] ?? null;

    $file_path = $proceso['fecha_titulacion_doc'] ?? '';
    if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
        $file_path = $uploads->uploadFile($_FILES['documento'], 'fecha_titulacion');
        require_once 'leer_fecha_pdf.php';
        $info = leerFechaTitulacionDesdePDF($file_path);
        if ($info['fecha']) $fecha_titulacion = $info['fecha'];
        if ($info['hora']) $hora_titulacion = $info['hora'];
        if ($info['presidente']) $presidente = $info['presidente'];
        if ($info['secretario']) $secretario = $info['secretario'];
        if ($info['vocal']) $vocal = $info['vocal'];
        if ($info['suplente']) $suplente = $info['suplente'];
    }

    $sql = "UPDATE proceso_titulacion SET 
            fecha_titulacion_status = ?, 
            fecha_titulacion_doc = ?, 
            fecha_titulacion_fecha = ?, 
            hora_titulacion = ?, 
            presidente = ?, 
            secretario = ?, 
            vocal = ?, 
            suplente = ?
            WHERE alumno_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssi", $status, $file_path, $fecha_titulacion, $hora_titulacion, $presidente, $secretario, $vocal, $suplente, $alumno_id);
    $stmt->execute();
     session_start(); // Asegúrate de que ya esté iniciado antes

$usuario = $_SESSION['usuario'] ?? 'N/A'; // Nombre del usuario actual
$paso = 'fecha_titulacion'; // 👈 Este es el paso que se debe guardar (DEFINIRLO)
$accion = 'Actualización de fecha_titulacion';

if (isset($_FILES['documento']) && $_FILES['documento']['error'] === UPLOAD_ERR_OK) {
    $accion .= " con archivo subido";
}

$sql_historial = "INSERT INTO historial_modificaciones (paso, accion, usuario, fecha, alumno_id)
                  VALUES (?, ?, ?, NOW(), ?)";

$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("sssi", $paso, $accion, $usuario, $alumno_id);
$stmt_historial->execute();
    break;

        }
        
        // Redireccionar a la vista de estados después de guardar
        header("Location: proceso_titulacion.php?id=" . $alumno_id);
        exit();
        
    } catch (Exception $e) {
        $message = '<div class="alert error">Error: ' . $e->getMessage() . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proceso de Titulación - <?php echo ucfirst($step); ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h1>Proceso de Titulación - <?php echo ucfirst($step); ?></h1>
        
        <?php echo $message; ?>
        
        <div class="step-container">
            <form action="proceso_detalle.php?alumno_id=<?php echo $alumno_id; ?>&step=<?php echo $step; ?>" method="POST" enctype="multipart/form-data">
                <form action="generar_oficio_asesor.php" method="POST">
                <?php switch ($step): 
                    case 'protocolo': ?>
                        <h2>Protocolo</h2>
                        
                        <div class="form-group">
                            <label for="documento">Subir oficio de protocolo (PDF):</label>
                            <input type="file" id="documento" name="documento" accept=".pdf">
                            <?php if (!empty($proceso['protocolo_doc'])): ?>
                                <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['protocolo_doc']); ?>" target="_blank">Ver documento</a></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="titulo_trabajo">Título de trabajo:</label>
                            <input type="text" id="titulo_trabajo" name="titulo_trabajo" 
                                   value="<?php echo htmlspecialchars($alumno['titulo_trabajo'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_registro">Número de registro:</label>
                            <input type="text" id="numero_registro" name="numero_registro" 
                                   value="<?php echo htmlspecialchars($proceso['numero_registro'] ?? ''); ?>" required>
                        </div>
                        <?php break;
                    
                   case 'asesor': ?>
    <div id="app">
        

        <div class="form-group">
            <label for="documento">Subir oficio de asesor (PDF):</label>
            <input type="file" id="documento" name="documento" accept=".pdf">
            <?php if (!empty($proceso['asesor_doc'])): ?>
                <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['asesor_doc']); ?>" target="_blank">Ver documento</a></p>
            <?php endif; ?>
        </div>  

       <hr>
<h2>Datos para el Oficio</h2>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="fecha_oficio"><strong>Fecha del oficio:</strong></label>
        <input type="date" name="fecha_oficio" class="form-control" placeholder="Ej. Jocotitlán, Estado de México, a 10 de febrero de 2025"
value="<?= $_POST['fecha_oficio'] ?? '' ?>">

    </div>

    <div class="col-md-6 form-group">
        <label for="numero_oficio"><strong>Número de oficio:</strong></label>
        <input type="text" name="numero_oficio" class="form-control" placeholder="Ej. Oficio No. 228C1701010103L/12/2025"
value="<?= $_POST['numero_oficio'] ?? '' ?>">

    </div>
</div>

<div class="row">
    <div class="col-md-6 form-group">
        <label for="nombre_estudiante"><strong>Nombre del estudiante:</strong></label>
        <input type="text" name="nombre_estudiante" class="form-control" placeholder="Ej. C. MARIO ANTONIO LÓPEZ OCTAVIANO"
value="<?= $_POST['nombre_estudiante'] ?? '' ?>">

    </div>

    <div class="col-md-6 form-group">
        <label for="opcion_titulacion"><strong>Opción de titulación:</strong></label>
        <input type="text" name="opcion_titulacion" class="form-control" placeholder="Ej. Titulación Integral en la modalidad Tesis Profesional"
value="<?= $_POST['opcion_titulacion'] ?? '' ?>">

    </div>
</div>

<div class="form-group" style="width: 100%;">
    <label for="nombre_proyecto"><strong>Nombre del proyecto:</strong></label>
<textarea id="nombre_proyecto" name="nombre_proyecto" rows="3" class="form-control" style="width: 100%;" placeholder="Ej. DISEÑO DE UNA INFRAESTRUCTURA DE RED..."><?= htmlspecialchars($_POST['nombre_proyecto'] ?? '') ?></textarea>

</div>





        <div class="asesores-container">
            <?php foreach ($asesores_guardados as $index => $asesor): ?>
                    <h2>Asesor</h2>

                <div class="asesor-group">
                   
                    <select name="asesores[]" class="select-asesor">
                        <option value="">Seleccione un asesor</option>
                        <?php foreach ($maestros as $maestro): ?>
                            <option value="<?= $maestro['nombre'] ?>" <?= $maestro['nombre'] === $asesor ? 'selected' : '' ?>>
                                <?= htmlspecialchars($maestro['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($index > 0): ?>
                        <button type="button" class="btn-remove-asesor">×</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <?php if (empty($asesores_guardados)): ?>
                <div class="asesor-group">
                    <label>Asesor:</label>
                    <select name="asesores[]" class="select-asesor">
                        <option value="">Seleccione un asesor</option>
                        <?php foreach ($maestros as $maestro): ?>
                            <option value="<?= $maestro['nombre'] ?>"><?= htmlspecialchars($maestro['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
        </div>

       

        <div class="form-group">
            <h2>Comisión Revisora</h2>
            <div class="comision-revisora">
                <?php for ($i = 0; $i < 3; $i++): ?>
                    <select name="comision_revisora[]" class="select-comision" required>
                        <option value="">Seleccione un maestro</option>
                        <?php foreach ($maestros as $maestro): ?>
                            <option value="<?= $maestro['nombre'] ?>" <?= (isset($comision_guardada[$i]) && $comision_guardada[$i] === $maestro['nombre']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($maestro['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endfor; ?>
            </div>
        </div>

       

<button 
  type="submit" 
  name="descargar_oficio" 
  style="margin-top: 10px; margin-left: 150px; position: relative; top: 66px; background-color: #0b0b67; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
  ⬇️ Descargar PDF
</button>






        <!-- Modal -->
        <div id="modalVistaPrevia" class="modal" style="display:none;">
            <div class="modal-content" style="background:white;padding:20px;border-radius:10px;">
                <span onclick="cerrarModal()" style="float:right;cursor:pointer;">&times;</span>
                <h2>Vista Previa del Oficio</h2>
                <div id="contenidoVistaPrevia" style="white-space: pre-wrap;"></div>
            </div>
        </div>
    </div>

<?php break; 


                    
                    case 'validacion': ?>
                        <h2>Validación</h2>
                        
                        <div class="form-group">
                            <label for="documento">Subir archivos para firma de validación (PDF):</label>
                            <input type="file" id="documento" name="documento" accept=".pdf">
                            <?php if (!empty($proceso['validacion_doc'])): ?>
                                <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['validacion_doc']); ?>" target="_blank">Ver documento</a></p>
                            <?php endif; ?>
                        </div>
                        <?php break;

                        

                    
                 case 'fecha_titulacion':
?>
    <h2>Fecha de Titulación</h2>


     <!-- Subida de documento PDF -->
    <div class="form-group">
        <label for="documento">Subir oficio de fecha de titulación (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['fecha_titulacion_doc'])): ?>
            <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['fecha_titulacion_doc']); ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>

    <!-- Campos adicionales del oficio -->
    <div class="form-group">
        <label for="fecha_oficio">Fecha del oficio:</label>
        <input type="date" id="fecha_oficio" name="fecha_oficio" class="form-control"
               value="<?php echo htmlspecialchars($proceso['fecha_oficio'] ?? ''); ?>"
                placeholder="Ej. 2025-02-10" required>
    </div>

    <div class="form-group">
        <label for="numero_oficio">Número de oficio:</label>
        <input type="text" id="numero_oficio" name="numero_oficio" class="form-control"
               value="<?php echo htmlspecialchars($proceso['numero_oficio'] ?? ''); ?>"
               placeholder="Ej. 228C1701010103L/16/2025">
    </div>

    <div class="form-group">
        <label for="nombre_estudiante">Nombre del estudiante:</label>
        <input type="text" id="nombre_estudiante" name="nombre_estudiante" class="form-control"
               value="<?php echo htmlspecialchars($proceso['nombre_estudiante'] ?? ''); ?>"
               placeholder="Ej. C. LORENA SÁNCHEZ GONZAGA">
    </div>

    <div class="form-group">
        <label for="numero_control">Número de control:</label>
        <input type="text" id="numero_control" name="numero_control" class="form-control"
               value="<?php echo htmlspecialchars($proceso['numero_control'] ?? ''); ?>"
               placeholder="Ej. 2014150480765">
    </div>

    <div class="form-group">
        <label for="opcion_titulacion">Opción de titulación:</label>
        <input type="text" id="opcion_titulacion" name="opcion_titulacion" class="form-control"
               value="<?php echo htmlspecialchars($proceso['opcion_titulacion'] ?? ''); ?>"
               placeholder="Ej. Examen General de Egreso de la Licenciatura (EGEL)">
    </div>


    <!-- Fecha y hora -->
    <div class="form-group">
        <label for="fecha_titulacion">Fecha de titulación:</label>
        <input type="date" id="fecha_titulacion" name="fecha_titulacion"
               value="<?php echo !empty($proceso['fecha_titulacion_fecha']) ? substr($proceso['fecha_titulacion_fecha'], 0, 10) : ''; ?>" required>
    </div>

    <div class="form-group">
        <label for="hora_titulacion">Hora de titulación:</label>
        <input type="time" id="hora_titulacion" name="hora_titulacion"
               value="<?php echo htmlspecialchars($proceso['hora_titulacion'] ?? ''); ?>" required>
    </div>

    <!-- Comité -->
    <div class="form-group">
        <label>Comité de titulación:</label>

        <div class="form-group">
            <label for="presidente">Presidente:</label>
            <select name="presidente" id="presidente" required>
                <option value="">Seleccione un presidente</option>
                <?php foreach ($maestros as $maestro): ?>
                    <option value="<?php echo $maestro['nombre']; ?>"
                        <?php if (($proceso['presidente'] ?? '') === $maestro['nombre']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($maestro['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="secretario">Secretario:</label>
            <select name="secretario" id="secretario" required>
                <option value="">Seleccione un secretario</option>
                <?php foreach ($maestros as $maestro): ?>
                    <option value="<?php echo $maestro['nombre']; ?>"
                        <?php if (($proceso['secretario'] ?? '') === $maestro['nombre']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($maestro['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="vocal">Vocal:</label>
            <select name="vocal" id="vocal" required>
                <option value="">Seleccione un vocal</option>
                <?php foreach ($maestros as $maestro): ?>
                    <option value="<?php echo $maestro['nombre']; ?>"
                        <?php if (($proceso['vocal'] ?? '') === $maestro['nombre']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($maestro['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="suplente">Suplente:</label>
            <select name="suplente" id="suplente">
                <option value="">Seleccione un suplente (opcional)</option>
                <?php foreach ($maestros as $maestro): ?>
                    <option value="<?php echo $maestro['nombre']; ?>"
                        <?php if (($proceso['suplente'] ?? '') === $maestro['nombre']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($maestro['nombre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Botón para descargar PDF -->
    <div style="margin-top: 30px;">
        <button type="submit" name="descargar_oficio_fecha"
            style="background-color: #0b0b67; color: white; padding: 10px 25px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ⬇️ Descargar PDF
        </button>
    </div>
<?php
break;



                        case 'registro_oficio': ?>
    <h2>Oficio Registro</h2>

    <div class="form-group">
        <label for="documento">Subir oficio de registro (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['registro_oficio_doc'])): ?>
            <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['registro_oficio_doc']); ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="numero_registro">Número de registro:</label>
        <input type="text" id="numero_registro" name="numero_registro" value="<?php echo htmlspecialchars($proceso['numero_registro'] ?? ''); ?>" required>
    </div>
<?php break;


case 'liberacion': ?>
    <h2>Liberacion</h2>
    <div class="form-group">
        <label for="documento">Subir documento de liberación (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['liberacion_doc'])): ?>
            <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['liberacion_doc']); ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>
<?php break;


case 'digitalizacion': ?>
    <div id="app">
        <h2>Digitalización</h2>

        <div class="form-group">
            <label for="documento">Subir archivo de digitalización (PDF):</label>
            <input type="file" id="documento" name="documento" accept=".pdf">
            <?php if (!empty($proceso['digitalizacion_doc'])): ?>
                <p>Documento actual: <a href="<?php echo $uploads->getFileUrl($proceso['digitalizacion_doc']); ?>" target="_blank">Ver documento</a></p>
            <?php endif; ?>
        </div>

        <hr>
        <h2>Datos para el Oficio</h2>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="fecha_oficio"><strong>Fecha del oficio:</strong></label>
                <input type="date" name="fecha_oficio" class="form-control" placeholder="Ej. Jocotitlán, Estado de México, a 10 de mayo de 2025"
                value="<?= $_POST['fecha_oficio'] ?? '' ?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="numero_oficio"><strong>Número de oficio:</strong></label>
                <input type="text" name="numero_oficio" class="form-control" placeholder="Ej. Oficio No. 228C1701010103L/12/2025"
                value="<?= $_POST['numero_oficio'] ?? '' ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="nombre_estudiante"><strong>Nombre del estudiante:</strong></label>
                <input type="text" name="nombre_estudiante" class="form-control" placeholder="Ej. C. MARIO ANTONIO LÓPEZ OCTAVIANO"
                value="<?= $_POST['nombre_estudiante'] ?? '' ?>">
            </div>

            <div class="col-md-6 form-group">
                <label for="opcion_titulacion"><strong>Opción de titulación:</strong></label>
                <input type="text" name="opcion_titulacion" class="form-control" placeholder="Ej. Titulación Integral en modalidad Tesis Profesional"
                value="<?= $_POST['opcion_titulacion'] ?? '' ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="nombre_proyecto"><strong>Nombre del proyecto:</strong></label>
            <textarea id="nombre_proyecto" name="nombre_proyecto" rows="3" class="form-control" style="width: 100%;" placeholder="Ej. DISEÑO DE UNA INFRAESTRUCTURA DE RED..."><?= htmlspecialchars($_POST['nombre_proyecto'] ?? '') ?></textarea>
        </div>

        <div class="row">
            <div class="col-md-6 form-group">
                <label for="numero_registro"><strong>Número de registro:</strong></label>
                <input type="text" name="numero_registro" class="form-control"
                value="<?= htmlspecialchars($proceso['numero_registro'] ?? '') ?>" required>
            </div>

            <div class="col-md-6 form-group">
                <label for="numero_cds"><strong>Número de CD's:</strong></label>
                <input type="number" name="numero_cds" class="form-control"
                value="<?= htmlspecialchars($proceso['numero_cds'] ?? '') ?>" required>
            </div>
        </div>

        <button 
            type="submit" 
            name="descargar_oficio_digitalizacion" 
            style="margin-top: 20px; margin-left: 150px; background-color: #0b0b67; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            ⬇️ Descargar PDF
        </button>
    </div>
<?php break; 



case 'numero_registro': ?>
    <h2>Número de Registro</h2>
    <div class="form-group">
        <label for="documento">Subir documento (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['numero_registro_doc'])): ?>
            <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['numero_registro_doc']) ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label for="numero_registro">Número de Registro:</label>
        <input type="text" id="numero_registro" name="numero_registro" value="<?= htmlspecialchars($proceso['numero_registro'] ?? '') ?>">
    </div>
<?php break;


case 'asignacion_revisores': ?>
    
<h2>Asignación de Revisores</h2>
<div class="form-group">
    <label for="documento">Subir archivo PDF:</label>
    <input type="file" id="documento" name="documento" accept=".pdf">
    <?php if (!empty($proceso['asignacion_revisores_doc'])): ?>
        <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['asignacion_revisores_doc']) ?>" target="_blank">Ver documento</a></p>
    <?php endif; ?>
</div>

<?php
$asesor = $proceso['asesor_nombres'] ?? '';
$comision_guardada = array_map('trim', explode(',', $proceso['comision_revisora1'] ?? ''));
$revisor1 = $comision_guardada[0] ?? '';
$revisor2 = $comision_guardada[1] ?? '';
$revisor3 = $comision_guardada[2] ?? '';
?>

<div class="form-group">
    <label for="asesor">Asesor:</label>
    <select name="asesor" id="asesor" required>
        <option value="">Seleccione un asesor</option>
        <?php foreach ($maestros as $m): ?>
            <option value="<?= $m['nombre'] ?>" <?= ($asesor === $m['nombre']) ? 'selected' : '' ?>>
                <?= $m['nombre'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<?php for ($i = 1; $i <= 3; $i++): ?>
    <div class="form-group">
        <label for="revisor<?= $i ?>">Revisor <?= $i ?>:</label>
        <select name="revisor<?= $i ?>" id="revisor<?= $i ?>" required>
            <option value="">Seleccione un revisor</option>
            <?php foreach ($maestros as $m): ?>
                <option value="<?= $m['nombre'] ?>" <?= (${ "revisor$i" } === $m['nombre']) ? 'selected' : '' ?>>
                    <?= $m['nombre'] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
<?php endfor; ?>

<?php break;

case 'liberacion': ?>
    <h2>Liberación</h2>
    <div class="form-group">
        <label for="documento">Subir documento de liberación (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['liberacion_doc'])): ?>
            <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['liberacion_doc']) ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>
<?php break;

case 'digitalizacion': ?>
    <h2>Digitalización</h2>
    <div class="form-group">
        <label for="documento">Subir archivo de digitalización (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['digitalizacion_doc'])): ?>
            <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['digitalizacion_doc']) ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label for="numero_registro">Número de registro:</label>
        <input type="text" id="numero_registro" name="numero_registro" value="<?= htmlspecialchars($proceso['numero_registro'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="numero_cds">Número de CD's:</label>
        <input type="number" id="numero_cds" name="numero_cds" value="<?= htmlspecialchars($proceso['numero_cds'] ?? '') ?>" min="1" required>
    </div>
<?php break; 
case 'fecha_titulacion': ?>
    <h2>Titulación</h2>
    <div class="form-group">
        <label for="documento">Subir oficio de titulación (PDF):</label>
        <input type="file" id="documento" name="documento" accept=".pdf">
        <?php if (!empty($proceso['fecha_titulacion_doc'])): ?>
            <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['fecha_titulacion_doc']) ?>" target="_blank">Ver documento</a></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="fecha_titulacion">Fecha de titulación:</label>
        <input type="date" id="fecha_titulacion" name="fecha_titulacion" value="<?= substr($proceso['fecha_titulacion_fecha'] ?? '', 0, 10) ?>" required>
    </div>
    <div class="form-group">
        <label for="hora_titulacion">Hora de titulación:</label>
        <input type="time" id="hora_titulacion" name="hora_titulacion" value="<?= $proceso['hora_titulacion'] ?? '' ?>" required>
    </div>

    <div class="form-group">
        <label for="presidente">Presidente:</label>
        <select name="presidente" id="presidente" required>
            <option value="">Seleccione un presidente</option>
            <?php foreach ($maestros as $m): ?>
                <option value="<?= $m ?>" <?= ($proceso['presidente'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="secretario">Secretario:</label>
        <select name="secretario" id="secretario" required>
            <option value="">Seleccione un secretario</option>
            <?php foreach ($maestros as $m): ?>
                <option value="<?= $m ?>" <?= ($proceso['secretario'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="vocal">Vocal:</label>
        <select name="vocal" id="vocal" required>
            <option value="">Seleccione un vocal</option>
            <?php foreach ($maestros as $m): ?>
                <option value="<?= $m ?>" <?= ($proceso['vocal'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="suplente">Suplente:</label>
        <select name="suplente" id="suplente">
            <option value="">Seleccione un suplente (opcional)</option>
            <?php foreach ($maestros as $m): ?>
                <option value="<?= $m ?>" <?= ($proceso['suplente'] ?? '') === $m ? 'selected' : '' ?>><?= $m ?></option>
            <?php endforeach; ?>
        </select>
    </div>
<?php break; 



case 'registro_carta_dual':
?>
    <h2>Registro/Carta</h2>
    <div class="form-group">
        <label for="registro_doc">Subir oficio de registro o carta (PDF):</label>
        <input type="file" name="registro_carta_dual_doc" id="registro_carta_dual_doc" accept=".pdf">
      <?php if (!empty($proceso['registro_carta_dual_doc'])): ?>
    <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['registro_carta_dual_doc']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>


    </div>
<?php
break;

case 'carta_postulacion':
?>
    <h2>Carta de Postulación</h2>
    <div class="form-group">
        <label for="carta_postulacion_doc">Subir carta de postulación (PDF):</label>
        <input type="file" name="carta_postulacion_doc" id="carta_postulacion_doc" accept=".pdf">
 <?php if (!empty($proceso['carta_postulacion_doc'])): ?>
    <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['carta_postulacion_doc']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>


    </div>
<?php
break;


case 'protocolos':
?>
    <h2>Protocolos</h2>
    <div class="form-group">
        <label for="anteproyecto">Subir anteproyecto (PDF):</label>
        <input type="file" name="protocolo_doc1" id="protocolo_doc1" accept=".pdf">
        <?php if (!empty($proceso['protocolo_doc1'])): ?>
    <p>Documento 1: <a href="<?= $uploads->getFileUrl($proceso['protocolo_doc1']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>
    </div>

    <div class="form-group">
        <label for="cronograma">Subir cronograma (PDF):</label>
       <input type="file" name="protocolo_doc2" id="protocolo_doc2" accept=".pdf">
       <?php if (!empty($proceso['protocolo_doc2'])): ?>
    <p>Documento 2: <a href="<?= $uploads->getFileUrl($proceso['protocolo_doc2']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>
    </div>

    <div class="form-group">
        <label for="aceptacion">Subir carta de aceptación (PDF):</label>
        <input type="file" name="protocolo_doc3" id="protocolo_doc3" accept=".pdf">
       <?php if (!empty($proceso['protocolo_doc3'])): ?>
    <p>Documento 3: <a href="<?= $uploads->getFileUrl($proceso['protocolo_doc3']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>
    </div>
<?php
break;

case 'oficio_resultados':
?>
    <h2>Oficio de Resultados</h2>
    <div class="form-group">
        <label for="oficio_resultados_doc">Subir oficio de resultados (PDF):</label>
        <input type="file" name="oficio_resultados_doc" id="oficio_resultados_doc" accept=".pdf">
        <?php if (!empty($proceso['oficio_resultados_doc'])): ?>
    <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['oficio_resultados_doc']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>

    </div>
<?php
break;

case 'registro':
?>
    <h2>Registro</h2>
    <div class="form-group">
        <label for="registro_doc">Subir documento de registro (PDF):</label>
        <input type="file" name="registro_doc" id="registro_doc" accept=".pdf">
       <?php if (!empty($proceso['registro_doc'])): ?>
    <p>Documento actual: <a href="<?= $uploads->getFileUrl($proceso['registro_doc']) ?>" target="_blank">Ver documento</a></p>
<?php endif; ?>

    </div>
<?php
break;


case 'oficio_aceptacion': ?>
    <div id="app">
        <h2>Oficio de Aceptación</h2>

        <div class="form-group">
            <label for="documento">Subir oficio de aceptación (PDF):</label>
            <input type="file" id="documento" name="documento" accept=".pdf">
            <?php if (!empty($proceso['oficio_aceptacion_doc'])): ?>
                <p>Documento actual: 
                    <a href="<?= $uploads->getFileUrl($proceso['oficio_aceptacion_doc']) ?>" target="_blank">Ver documento</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
<?php break;


                endswitch; ?>
                
                <div class="form-actions">
                    <button type="submit" name="guardar" class="btn">Guardar</button>
                    <button type="submit" name="finalizar" class="btn primary">Finalizar</button>
                </div>
            </form>
        </div>
    </div>
                <script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_protocolo_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            document.getElementById('titulo_trabajo').value = data.titulo;
            document.getElementById('numero_registro').value = data.numero_registro;
        }
    });
});
</script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('documento');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const formData = new FormData();
            formData.append('archivo_pdf', fileInput.files[0]);

            fetch('leer_fecha_pdf.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    document.getElementById('fecha_titulacion').value = data.fecha;
                    document.getElementById('hora_titulacion').value = data.hora;
                    seleccionarPorTexto('presidente', data.presidente);
                    seleccionarPorTexto('secretario', data.secretario);
                    seleccionarPorTexto('vocal', data.vocal);
                    seleccionarPorTexto('suplente', data.suplente);
                }
            });
        });
    }

    function seleccionarPorTexto(id, texto) {
        const select = document.getElementById(id);
        for (let option of select.options) {
            if (option.text.trim() === texto.trim()) {
                option.selected = true;
                break;
            }
        }
    }
});

        // Validación de comisión revisora (3 miembros)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const comisionSelects = document.querySelectorAll('select[name="comision_revisora[]"]');
                if (comisionSelects.length > 0) {
                    const selected = Array.from(comisionSelects).filter(select => select.value !== '');
                    
                    if (selected.length !== 3) {
                        e.preventDefault();
                        alert('Debe seleccionar exactamente 3 maestros para la comisión revisora');
                        return false;
                    }
                    
                    const valores = selected.map(select => select.value);
                    if (new Set(valores).size !== valores.length) {
                        e.preventDefault();
                        alert('No puede seleccionar el mismo maestro más de una vez en la comisión revisora');
                        return false;
                    }
                }
                
                // Validación de asesores (al menos 1)
                const asesorSelects = document.querySelectorAll('select[name="asesores[]"]');
                if (asesorSelects.length > 0) {
                    const asesoresSelected = Array.from(asesorSelects).filter(select => select.value !== '');
                    
                    if (asesoresSelected.length === 0) {
                        e.preventDefault();
                        alert('Debe seleccionar al menos un asesor');
                        return false;
                    }
                    
                    const valoresAsesores = asesoresSelected.map(select => select.value);
                    if (new Set(valoresAsesores).size !== valoresAsesores.length) {
                        e.preventDefault();
                        alert('No puede seleccionar el mismo asesor más de una vez');
                        return false;
                    }
                }
                
                // Validación de roles en comité de titulación
                const presidente = document.getElementById('Presidente').value;
                const secretario = document.getElementById('Secretario').value;
                const vocal = document.getElementById('Vocal').value;
                const suplente = document.getElementById('Suplente').value;
                
                const roles = [presidente, secretario, vocal];
                if (suplente) roles.push(suplente);
                
                if (new Set(roles).size !== roles.length) {
                    e.preventDefault();
                    alert('No puede asignar la misma persona a múltiples roles en el comité de titulación');
                    return false;



                    
        document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_fecha_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
        } else {
            document.getElementById('fecha_titulacion').value = data.fecha;
            document.getElementById('hora_titulacion').value = data.hora;
            seleccionarPorTexto('presidente', data.presidente);
            seleccionarPorTexto('secretario', data.secretario);
            seleccionarPorTexto('vocal', data.vocal);
            seleccionarPorTexto('suplente', data.suplente);
        }
    });
});
                }
            });
        }



        // Agregar más asesores dinámicamente
        const agregarAsesorBtn = document.getElementById('agregar-asesor');
        if (agregarAsesorBtn) {
            agregarAsesorBtn.addEventListener('click', function() {
                const container = document.querySelector('.asesores-container');
                if (container) {
                    const newGroup = document.createElement('div');
                    newGroup.className = 'asesor-group';
                    newGroup.innerHTML = `
                        <select name="asesores[]" class="select-asesor">
                            <option value="">Seleccione un asesor</option>
                            <?php foreach ($maestros as $maestro): ?>
                                <option value="<?php echo $maestro['nombre']; ?>">
                                    <?php echo htmlspecialchars($maestro['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="button" class="btn-remove-asesor">×</button>
                    `;
                    container.appendChild(newGroup);
                }
            });
        }

        // Eliminar asesores
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-remove-asesor')) {
                e.target.closest('.asesor-group').remove();
            }
        });
    
    </script>

    <?php if ($step === 'asesor'): ?>
<script src="https://cdn.jsdelivr.net/npm/vue@3"></script>
<script>
const app = Vue.createApp({
    data() {
        return {
            asesor: '',
            revisor1: '',
            revisor2: '',
            revisor3: ''
        };
    },
    mounted() {
        document.getElementById('documento').addEventListener('change', this.procesarPDF);
    },
    methods: {
    procesarPDF(event) {
        const archivo = event.target.files[0];
        const formData = new FormData();
        formData.append('archivo_pdf', archivo);

        fetch('leer_pdf.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            this.asesor = data.asesor;
            this.revisor1 = data.revisor1;
            this.revisor2 = data.revisor2;
            this.revisor3 = data.revisor3;

            // Selecciona el asesor en el select real
            const asesorSelect = document.querySelector('select[name="asesores[]"]');
            if (asesorSelect) {
                for (let opt of asesorSelect.options) {
                    if (opt.value.trim() === data.asesor.trim()) {
                        opt.selected = true;
                        break;
                    }
                }
            }

            // Selecciona revisores
            const comisionSelects = document.querySelectorAll('select[name="comision_revisora[]"]');
            const revisores = [data.revisor1, data.revisor2, data.revisor3];
            comisionSelects.forEach((select, index) => {
                const valor = revisores[index];
                for (let opt of select.options) {
                    if (opt.value.trim() === valor.trim()) {
                        opt.selected = true;
                        break;
                    }
                }
            });
        })

    }
}

});
app.mount('#app');
</script>
<?php endif; ?>


<script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_oficio_registro_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.numero_registro) {
            document.getElementById('numero_registro').value = data.numero_registro;
        } else if (data.error) {
            alert(data.error);
        }
    });
});
</script>


<script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_digitalizacion_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.numero_registro) {
            document.getElementById('numero_registro').value = data.numero_registro;
        }
        if (data.numero_cds) {
            document.getElementById('numero_cds').value = data.numero_cds;
        }
    })
});
</script>


<script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_oficio_registro_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.numero_registro) {
            document.getElementById('numero_registro').value = data.numero_registro;
        } else if (data.error) {
            alert(data.error);
        }
    });
});
</script>

<script>
document.getElementById('documento').addEventListener('change', function () {
    const archivo = this.files[0];
    if (!archivo) {
        alert("Selecciona un archivo primero.");
        return;
    }

    const formData = new FormData();
    formData.append('archivo_pdf', archivo); // importante: 'archivo_pdf' es lo que PHP espera

    fetch('leer_asesor_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        console.log("Detectado:", data);
        if (data.asesor) document.getElementById('asesor').value = data.asesor;
        ['revisor1', 'revisor2', 'revisor3'].forEach(id => {
            if (data[id]) document.getElementById(id).value = data[id];
        });
    })
    .catch(error => {
        alert("Error al leer el PDF: " + error);
    });
});

</script>

<script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_digitalizacion_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.numero_registro) document.getElementById('numero_registro').value = data.numero_registro;
        if (data.numero_cds) document.getElementById('numero_cds').value = data.numero_cds;
    });
});
</script>

<script>
document.getElementById('documento').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('archivo_pdf', this.files[0]);

    fetch('leer_fecha_pdf.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.fecha) document.getElementById('fecha_titulacion').value = data.fecha;
        if (data.hora) document.getElementById('hora_titulacion').value = data.hora;
        if (data.presidente) seleccionarPorTexto('presidente', data.presidente);
        if (data.secretario) seleccionarPorTexto('secretario', data.secretario);
        if (data.vocal) seleccionarPorTexto('vocal', data.vocal);
        if (data.suplente) seleccionarPorTexto('suplente', data.suplente);
    });

    function seleccionarPorTexto(id, texto) {
        const select = document.getElementById(id);
        for (let opt of select.options) {
            if (opt.value.trim() === texto.trim()) {
                opt.selected = true;
                break;
            }
        }
    }
});
</script>



<script>
function previsualizarOficio() {
    const fecha = document.getElementById('fecha_oficio').value;
    const numero = document.getElementById('numero_oficio').value;
    const estudiante = document.getElementById('nombre_estudiante').value;
    const proyecto = document.getElementById('nombre_proyecto').value;
    const opcion = document.getElementById('opcion_titulacion').value;

    const contenido = `
${fecha}

${numero}

P R E S E N T E

Por medio del presente se hace constar que:

${estudiante}

Está presentando el siguiente proyecto:

"${proyecto}"

Bajo la opción de titulación:

${opcion}
    `;
    document.getElementById("contenidoVistaPrevia").textContent = contenido;
    document.getElementById("modalVistaPrevia").style.display = "block";
}

function cerrarModal() {
    document.getElementById("modalVistaPrevia").style.display = "none";
}
</script>



</body>
</html>
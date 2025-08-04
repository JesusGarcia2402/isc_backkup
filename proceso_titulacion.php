<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/Uploads.php';

$db = new Database();
$conn = $db->getConnection();

if (!isset($_GET['id'])) {
    header("Location: consultar.php");
    exit();
}

$alumno_id = intval($_GET['id']);

$sql_alumno = "SELECT * FROM alumnos WHERE id = ?";
$stmt_alumno = $conn->prepare($sql_alumno);
$stmt_alumno->bind_param("i", $alumno_id);
$stmt_alumno->execute();
$result_alumno = $stmt_alumno->get_result();
$alumno = $result_alumno->fetch_assoc();

$sql_update_user = "UPDATE proceso_titulacion SET ultima_modificacion_por = ? WHERE alumno_id = ?";
$stmt_update_user = $conn->prepare($sql_update_user);
$stmt_update_user->bind_param("si", $usuario, $alumno_id);
$stmt_update_user->execute();

if (!$alumno) {
    header("Location: consultar.php");
    exit();
}

$sql_proceso = "SELECT * FROM proceso_titulacion WHERE alumno_id = ?";
$stmt_proceso = $conn->prepare($sql_proceso);
$stmt_proceso->bind_param("i", $alumno_id);
$stmt_proceso->execute();
$result_proceso = $stmt_proceso->get_result();
$proceso = $result_proceso->fetch_assoc();

$modalidad = $alumno['modalidad_titulacion'];

$pasos_modalidad = [];
switch ($modalidad) {
    case 'Titulación Integral en la modalidad de Tesis Profesional':
        $pasos_modalidad = ['protocolo_status', 'asesor_status', 'registro_oficio_status', 'liberacion_status', 'digitalizacion_status', 'fecha_titulacion_status'];
        break;
    case 'Titulación Integral en la modalidad de Examen General de Egreso de Licenciatura (EGEL)':
        $pasos_modalidad = ['numero_registro_status', 'oficio_resultados_status', 'fecha_titulacion_status'];
        break;
    case 'Titulación Integral en la modalidad de Residencia Profesional':
        $pasos_modalidad = ['carta_postulacion_status', 'protocolos_status', 'digitalizacion_status', 'fecha_titulacion_status'];
        break;
    case 'Titulación Integral en la modalidad de Proyecto de Investigación':
        $pasos_modalidad = ['oficio_aceptacion_status', 'numero_registro_status', 'asesor_status', 'liberacion_status', 'digitalizacion_status', 'fecha_titulacion_status'];
        break;
    case 'Titulación Integral en la modalidad de Promedio General Sobresaliente':
        $pasos_modalidad = ['registro_status', 'protocolo_status', 'fecha_titulacion_status'];
        break;
    case 'Titulación Integral en la modalidad de Proyecto Integral de Educación Dual':
        $pasos_modalidad = ['registro_carta_dual_status', 'digitalizacion_status', 'fecha_titulacion_status'];
        break;
}

$total_pasos = count($pasos_modalidad);
$completados = count(array_filter($pasos_modalidad, fn($campo) => $proceso[$campo] === 'completado'));
$porcentaje = round(($completados / $total_pasos) * 100);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proceso de Titulación</title>
    <link rel="stylesheet" href="assets/proceso_titulacion.css">
    <style>
        .arrow {
            font-size: 24px;
            color: #333;
        }
        .alumno-panel {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }
        .alumno-info {
            flex: 1;
        }

        /* Barra de progreso */
        .barra-progreso-container {
            margin: -20px auto 20px auto;
            width: 80%;
            background-color: #f1f1f1;
            border-radius: 30px;
            height: 30px;
            box-shadow: inset 0 1px 4px rgba(0,0,0,0.2);
        }
        .barra-progreso {
            height: 100%;
            border-radius: 30px;
            width: 0;
            line-height: 30px;
            text-align: center;
            color: white;
            font-weight: bold;
            transition: width 0.5s ease;
        }

        .proceso-circulos {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>
<body>
<header class="header-login">
    <img src="imagenes/tesjo.jpg" alt="TESJo">
    <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
    <img src="imagenes/nacional2.jpeg" alt="TecNM">
</header>

<div class="container">
    <h1>Proceso de Titulación</h1>
    <a href="consultar.php" class="btn back">← Regresar a Consultar</a>

    <div class="alumno-panel">
        <div class="alumno-info">
            <h2>Información del Alumno</h2>
            <p><strong>Número de Control:</strong> <?= htmlspecialchars($alumno['numero_control']) ?></p>
            <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($alumno['nombre_completo']) ?></p>
            <p><strong>Modalidad:</strong> <?= htmlspecialchars($modalidad) ?></p>
            <?php if (!empty($alumno['fecha_inicio'])): ?>
                <p><strong>Fecha de Inicio:</strong> <?= date("d/m/Y", strtotime($alumno['fecha_inicio'])) ?></p>
            <?php endif; ?>
            <?php if (!empty($alumno['fecha_termino'])): ?>
                <p><strong>Fecha de Término:</strong> <?= date("d/m/Y", strtotime($alumno['fecha_termino'])) ?></p>
            <?php endif; ?>
            <p><strong>Tiempo restante:</strong> <span id="contador"></span></p>
        </div>
    </div>

    <!-- Barra de Progreso -->
    <div class="barra-progreso-container">
        <div class="barra-progreso" id="barra" style="width: <?= $porcentaje ?>%; background-color: #1e88e5;">
            <?= $porcentaje ?>%
        </div>
    </div>

    <div class="proceso-container">
        <div class="proceso-circulos">
        <?php
            function paso($nombre, $etiqueta, $status, $id) {
                return '<a href="proceso_detalle.php?alumno_id=' . $id . '&step=' . $nombre . '" class="circulo-link">
                            <div class="circulo ' . ($status === "completado" ? "completado" : "") . '">
                                <h3>' . $etiqueta . '</h3>
                            </div>
                        </a>';
            }
            $flecha = '<span class="arrow">➔</span>';
            switch ($modalidad) {
                case 'Titulación Integral en la modalidad de Tesis Profesional':
                    echo paso('protocolo', 'Protocolo', $proceso['protocolo_status'], $alumno_id) . $flecha .
                         paso('asesor', 'Asesor', $proceso['asesor_status'], $alumno_id) . $flecha .
                         paso('registro_oficio', 'Oficio Registro', $proceso['registro_oficio_status'], $alumno_id) . $flecha .
                         paso('liberacion', 'Liberación', $proceso['liberacion_status'], $alumno_id) . $flecha .
                         paso('digitalizacion', 'Digitalización', $proceso['digitalizacion_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
                case 'Titulación Integral en la modalidad de Examen General de Egreso de Licenciatura (EGEL)':
                    echo paso('numero_registro', 'Número Registro', $proceso['numero_registro_status'], $alumno_id) . $flecha .
                         paso('oficio_resultados', 'Oficio Resultados', $proceso['oficio_resultados_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
                case 'Titulación Integral en la modalidad de Residencia Profesional':
                    echo paso('carta_postulacion', 'Carta Postulación', $proceso['carta_postulacion_status'], $alumno_id) . $flecha .
                         paso('protocolos', 'Protocolos', $proceso['protocolos_status'], $alumno_id) . $flecha .
                         paso('digitalizacion', 'Digitalización', $proceso['digitalizacion_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
                case 'Titulación Integral en la modalidad de Proyecto de Investigación':
                    echo paso('oficio_aceptacion', 'Oficio Aceptación', $proceso['oficio_aceptacion_status'], $alumno_id) . $flecha .
                         paso('numero_registro', 'Número Registro', $proceso['numero_registro_status'], $alumno_id) . $flecha .
                         paso('asesor', 'Asesor', $proceso['asesor_status'], $alumno_id) . $flecha .
                         paso('liberacion', 'Liberación', $proceso['liberacion_status'], $alumno_id) . $flecha .
                         paso('digitalizacion', 'Digitalización', $proceso['digitalizacion_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
                case 'Titulación Integral en la modalidad de Promedio General Sobresaliente':
                    echo paso('registro', 'Registro', $proceso['registro_status'], $alumno_id) . $flecha .
                         paso('protocolo', 'Protocolo', $proceso['protocolo_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
                case 'Titulación Integral en la modalidad de Proyecto Integral de Educación Dual':
                    echo paso('registro_carta_dual', 'Registro/Carta', $proceso['registro_carta_dual_status'], $alumno_id) . $flecha .
                         paso('digitalizacion', 'Digitalización', $proceso['digitalizacion_status'], $alumno_id) . $flecha .
                         paso('fecha_titulacion', 'Titulación', $proceso['fecha_titulacion_status'], $alumno_id);
                    break;
            }
        ?>
        </div>
    </div>
</div>

<script>
const fechaTermino = "<?= $alumno['fecha_termino'] ?>";
function iniciarCuentaRegresiva(fechaFin) {
    const [anio, mes, dia] = fechaFin.split("-");
    const fin = new Date(anio, mes - 1, dia - 1, 23, 59, 59).getTime();
    const contador = document.getElementById("contador");
    const intervalo = setInterval(() => {
        const ahora = new Date().getTime();
        const diferencia = fin - ahora;
        if (diferencia < 0) {
            clearInterval(intervalo);
            contador.innerHTML = "¡Tiempo vencido!";
            contador.style.color = "red";
            return;
        }
        const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
        const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
        const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);
        contador.innerHTML = `${dias} días, ${horas}h ${minutos}m ${segundos}s`;
    }, 1000);
}
iniciarCuentaRegresiva(fechaTermino);

// Cambiar color dinámico de barra
const barra = document.getElementById("barra");
const porcentaje = <?= $porcentaje ?>;
if (porcentaje <= 10) barra.style.backgroundColor = "#e53935";
else if (porcentaje <= 30) barra.style.backgroundColor = "#f4511e";
else if (porcentaje <= 50) barra.style.backgroundColor = "#ffb300";
else if (porcentaje <= 70) barra.style.backgroundColor = "#c0ca33";
else if (porcentaje <= 90) barra.style.backgroundColor = "#43a047";
else barra.style.backgroundColor = "#1e88e5";
</script>

</body>
</html>

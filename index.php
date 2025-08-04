<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

// Obtener estadísticas por año
$sql_anios = "
    SELECT 
        YEAR(fecha_titulacion_fecha) AS anio, 
        COUNT(DISTINCT alumno_id) AS cantidad 
    FROM proceso_titulacion 
    WHERE fecha_titulacion_status = 'completado' 
        AND fecha_titulacion_fecha IS NOT NULL
    GROUP BY anio
    ORDER BY anio
";

$result_anios = $conn->query($sql_anios);
$datos_grafica = [];
$total_titulados = 0;
while ($row = $result_anios->fetch_assoc()) {
    $datos_grafica[] = $row;
    $total_titulados += $row['cantidad'];
}

// Obtener conteo de alumnos en proceso
$sql_proceso = "SELECT 
    COUNT(CASE WHEN protocolo_status = 'pendiente' THEN 1 END) as pendientes,
    COUNT(CASE WHEN protocolo_status = 'en-progreso' THEN 1 END) as en_progreso,
    COUNT(CASE WHEN protocolo_status = 'completado' AND fecha_titulacion_status != 'completado' THEN 1 END) as en_titulacion,
    COUNT(CASE WHEN fecha_titulacion_status = 'completado' THEN 1 END) as titulados
    FROM proceso_titulacion";
$result_proceso = $conn->query($sql_proceso);
$estadisticas_proceso = $result_proceso->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Titulación - Menú Principal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/index.css">
    <link rel="stylesheet" href="assets/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<header class="header-principal">
    <img src="imagenes/tesjo.jpg" alt="Logo Izquierdo" class="logo-img">
    <div class="logo-titulo">Titulación.<span>ISC</span></div>
    <img src="imagenes/nacional2.jpeg" alt="Logo Derecho" class="logo-img">
</header>

<nav class="nav-principal">
    <ul>
        <li><a href="registrar.php">Registrar</a></li>
        <li><a href="consultar.php">Consultar</a></li>
        <li><a href="division.php">División</a></li>
        <li><a href="historial.php">Historial</a></li>
        <li><a href="respaldo.php">Respaldo</a></li>
        <li><a href="generar_dashboard_pdf.php">Reportes</a></li>
        <?php if ($_SESSION['usuario'] === 'Juan Carlos Ambriz Polo'): ?>
            <li><a href="agregar_usuario.php">Agregar Usuario</a></li>
        <?php endif; ?>
        <li><a href="logout.php">Cerrar Sesión</a></li>
    </ul>
</nav>

<section class="hero">
    <h1>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h1>
    <p>Gestiona el proceso de titulación de manera profesional.</p>
</section>

<main class="container">
    <h2 class="titulo-dashboard">Resumen Ejecutivo de Titulación</h2>

    <?php
    // Alumnos detenidos (sin modificación en 15 días)
    $sql_det = "SELECT COUNT(*) AS detenidos 
                FROM proceso_titulacion 
                WHERE TIMESTAMPDIFF(DAY, ultima_modificacion_fecha, NOW()) > 15 
                AND fecha_titulacion_status != 'completado'";
    $res_det = $conn->query($sql_det);
    $fila_det = $res_det->fetch_assoc();
    $procesos_detenidos = $fila_det['detenidos'];

    // Total alumnos registrados
    $sql_total_alumnos = "SELECT COUNT(*) AS total FROM proceso_titulacion";
    $res_total = $conn->query($sql_total_alumnos);
    $fila_total = $res_total->fetch_assoc();
    $total_registrados = $fila_total['total'];

    // Tasa de éxito
    $tasa_exito = $total_registrados > 0 
        ? round(($total_titulados / $total_registrados) * 100, 2) 
        : 0;
    ?>

    <div class="dashboard-grid">
        <div class="card resumen-card">
            <h3>Total Titulados</h3>
            <p class="numero"><?= $total_titulados ?></p>
            <span class="detalle">Con examen aprobado</span>
        </div>
        <div class="card resumen-card">
            <h3>En Proceso</h3>
            <p class="numero"><?= $estadisticas_proceso['en_progreso'] + $estadisticas_proceso['pendientes'] + $estadisticas_proceso['en_titulacion'] ?></p>
            <span class="detalle">Incluye todas las fases activas</span>
        </div>
        <div class="card resumen-card">
            <h3>Procesos Detenidos</h3>
            <p class="numero"><?= $procesos_detenidos ?></p>
            <span class="detalle">Sin avance en los últimos 15 días</span>
        </div>
        <div class="card resumen-card">
            <h3>Tasa de Éxito</h3>
            <p class="numero"><?= $tasa_exito ?>%</p>
            <span class="detalle">Titulados vs registrados</span>
        </div>
    </div>

 


<div class="card">
    <h3>Detalle por Alumno</h3>
    <button class="btn-detalle" onclick="toggleDetalle()">🔍 Ver Detalle por Alumno</button>

    <div id="tablaDetalle" style="display:none; margin-top: 20px;">
        <table class="tabla-detalle">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>No. Control</th>
                    <th>Modalidad</th>
                    <th>Estado</th>
                    <th>Última Modificación</th>
                
                </tr>
            </thead>
            <tbody>
                <?php
                $sql_detalle = "SELECT 
                                    a.nombre_completo, 
                                    a.numero_control, 
                                    a.modalidad_titulacion,
                                    CASE 
                                        WHEN p.fecha_titulacion_status = 'completado' THEN 'TITULADO'
                                        WHEN p.protocolo_status = 'completado' THEN 'EN TITULACIÓN'
                                        WHEN p.protocolo_status = 'en-progreso' THEN 'EN PROCESO'
                                        ELSE 'PENDIENTE'
                                    END AS estado,
                                    p.ultima_modificacion_fecha,
                                    p.ultima_modificacion_por
                                FROM proceso_titulacion p
                                JOIN alumnos a ON p.alumno_id = a.id
                                ORDER BY p.ultima_modificacion_fecha DESC";
                $res_detalle = $conn->query($sql_detalle);
                while ($fila = $res_detalle->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($fila['numero_control']) ?></td>
                        <td><?= htmlspecialchars($fila['modalidad_titulacion']) ?></td>
                        <td><?= $fila['estado'] ?></td>
                        <td><?= date('d/m/Y', strtotime($fila['ultima_modificacion_fecha'])) ?></td>
                      
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>







</main>

<script>
    const ctx = document.getElementById('graficaTitulados').getContext('2d');
    const datos = <?php echo json_encode($datos_grafica); ?>;
    const labels = datos.map(item => item.anio);
    const valores = datos.map(item => item.cantidad);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Alumnos Titulados',
                data: valores,
                backgroundColor: 'rgba(0, 224, 255, 0.6)',
                borderColor: 'rgba(0, 224, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }


        
    });
    function toggleDetalle() {
    const tabla = document.getElementById('tablaDetalle');
    tabla.style.display = (tabla.style.display === 'none') ? 'block' : 'none';
}
</script>


</body>
</html>
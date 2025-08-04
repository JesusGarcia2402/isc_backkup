<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$alumnos = [];
$modalidad_seleccionada = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['modalidad'])) {
    $modalidad_seleccionada = trim($_GET['modalidad']);

    if ($modalidad_seleccionada === 'Titulación Integral en la modalidad de Proyecto de Innovación Tecnológica') {
        header("Location: construccion.php");
        exit();
    }

    if (!empty($modalidad_seleccionada)) {
        $sql = "SELECT a.*, 
                       p.protocolo_status, p.asesor_status, p.validacion_status, p.fecha_titulacion_status
                FROM alumnos a
                LEFT JOIN proceso_titulacion p ON a.id = p.alumno_id
                WHERE a.modalidad_titulacion = ?
                ORDER BY a.nombre_completo";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $modalidad_seleccionada);
        $stmt->execute();
        $result = $stmt->get_result();
        $alumnos = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consultar Alumnos</title>
    <link rel="stylesheet" href="assets/consultar.css">
</head>
<body>

    <!-- ENCABEZADO CON LOGOS -->
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="Logo Izquierdo">
        <div class="titulo">Tecnológico de Estudios Superiores de Jocotitlán</div>
        <img src="imagenes/nacional2.jpeg" alt="Logo Derecho">
    </header>

    <div class="container">
        <h1>Consultar Alumnos</h1>
        <hr>
        <a href="index.php" class="btn back">Regresar al Menú</a>

        <div id="app">
            <form method="GET" action="consultar.php">
                <div class="form-group">
                    <label for="plan_estudio">Seleccione Plan de Estudio:</label>
                    <select id="plan_estudio" v-model="plan" class="form-control" required>
                        <option value="2016">Plan 2009/2010 a partir de 2016</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modalidad">Seleccione Modalidad de Titulación:</label>
                    <select id="modalidad" name="modalidad" v-model="modalidad" class="form-control" required>
                        <option value="">-- Seleccione una modalidad --</option>
                        <option v-for="mod in modalidadesFiltradas" :value="mod">{{ mod }}</option>
                    </select>
                </div>

                <button type="submit" class="btn">Buscar</button>
            </form>
        </div>

        <?php if (!empty($alumnos)): ?>
            <div class="results">
                <h2>Alumnos en modalidad: <?php echo htmlspecialchars($modalidad_seleccionada); ?></h2>
                
                <table>
                    <thead>
                        <tr>
                            <th>No. Control</th>
                            <th>Nombre Completo</th>
                            <th>Modalidad</th>
                            <th>Estado Titulación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($alumnos as $alumno): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($alumno['numero_control']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['nombre_completo']); ?></td>
                                <td><?php echo htmlspecialchars($alumno['modalidad_titulacion']); ?></td>
                                <td>
                                    <?php 
                                        $estado = 'Pendiente';
                                        if ($alumno['fecha_titulacion_status'] === 'completado') {
                                            $estado = 'Titulado';
                                        } elseif ($alumno['validacion_status'] === 'completado') {
                                            $estado = 'En validación';
                                        } elseif ($alumno['asesor_status'] === 'completado') {
                                            $estado = 'Con asesor';
                                        } elseif ($alumno['protocolo_status'] === 'completado') {
                                            $estado = 'Protocolo registrado';
                                        }
                                        echo $estado;
                                    ?>
                                </td>
                                <td>
                                    <a href="proceso_titulacion.php?id=<?php echo $alumno['id']; ?>" class="btn small">Ver proceso</a>
                                    <a href="eliminar_alumno.php?id=<?php echo $alumno['id']; ?>" class="btn small danger" onclick="return confirm('¿Estás seguro de que deseas eliminar este alumno?');">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif (!empty($modalidad_seleccionada)): ?>
            <div class="alert info">No se encontraron alumnos en la modalidad "<?php echo htmlspecialchars($modalidad_seleccionada); ?>"</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script>
    const { createApp } = Vue;

    createApp({
        data() {
            return {
                plan: '2016',
                modalidad: '',
                modalidades: {
                    "2016": [
                        "Titulación Integral en la modalidad de Tesis Profesional",
                        "Titulación Integral en la modalidad de Proyecto de Investigación",
                        "Titulación Integral en la modalidad de Promedio General Sobresaliente",
                        "Titulación Integral en la modalidad de Examen General de Egreso de Licenciatura (EGEL)",
                        "Titulación Integral en la modalidad de Residencia Profesional",
                        "Titulación Integral en la modalidad de Proyecto de Innovación Tecnológica",
                        "Titulación Integral en la modalidad de Proyecto Integral de Educación Dual"
                    ]
                }
            }
        },
        computed: {
            modalidadesFiltradas() {
                return this.modalidades[this.plan] || [];
            }
        }
    }).mount('#app');
    </script>
</body>
</html>

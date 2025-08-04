<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

$db = new Database();
$conn = $db->getConnection();

$message = '';

// Obtener lista de maestros
$sql_maestros = "SELECT id, nombre FROM maestros WHERE activo = TRUE ORDER BY nombre";
$maestros = $conn->query($sql_maestros)->fetch_all(MYSQLI_ASSOC);

// Modalidades de titulación
$modalidades = [
    'Titulación Integral en la modalidad de Tesis Profesional',
    'Titulación Integral en la modalidad de Proyecto de Investigación',
    'Titulación Integral en la modalidad de Promedio General Sobresaliente',
    'Titulación Integral en la modalidad de Examen General de Egreso de Licenciatura (EGEL)',
    'Titulación Integral en la modalidad de Residencia Profesional',
    'Titulación Integral en la modalidad de Proyecto de Innovación Tecnológica',
    'Titulación Integral en la modalidad de Proyecto Integral de Educación Dual'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero_control = $_POST['numero_control'];
    $nombre_completo = $_POST['nombre_completo'];
    $modalidad_titulacion = $_POST['modalidad_titulacion'];
    $fecha_inicio = $_POST['fecha_inicio'];
$fecha_termino = $_POST['fecha_termino'];


    if (empty($numero_control) || empty($nombre_completo) || empty($modalidad_titulacion)) {
        $message = '<div class="alert error">Todos los campos son obligatorios</div>';
    } else {
        $sql = "INSERT INTO alumnos (numero_control, nombre_completo, modalidad_titulacion, fecha_inicio, fecha_termino) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $numero_control, $nombre_completo, $modalidad_titulacion, $fecha_inicio, $fecha_termino);

        if ($stmt->execute()) {
            $alumno_id = $stmt->insert_id;
            $sql_proceso = "INSERT INTO proceso_titulacion (alumno_id) VALUES (?)";
            $stmt_proceso = $conn->prepare($sql_proceso);
            $stmt_proceso->bind_param("i", $alumno_id);
            $stmt_proceso->execute();

            header("Location: index.php");
            exit;
        } else {
            $message = '<div class="alert error">Error al registrar alumno: ' . $conn->error . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Alumno</title>
    <link rel="stylesheet" href="assets/registrar.css">
</head>
<body>

    <!-- ENCABEZADO CON LOGOS -->
    <header class="header-login">
        <img src="imagenes/tesjo.jpg" alt="TESJo" style="height: 60px;">
        <div class="titulo">
            Tecnológico de Estudios Superiores de Jocotitlán
        </div>
        <img src="imagenes/nacional2.jpeg" alt="TecNM" style="height: 60px;">
    </header>

    <!-- CONTENEDOR DE REGISTRO -->
    <div class="container">
        <div class="register-box">
            <h2>Registrar Nuevo Alumno</h2>
            <?php echo $message; ?>

            <div id="app">
                <form action="registrar.php" method="POST">
                    <div class="form-group">
                        <label for="numero_control">Número de Control:</label>
                        <input type="text" id="numero_control" name="numero_control" v-model="numero_control" required>
                    </div>

                    <div class="form-group">
                        <label for="nombre_completo">Nombre Completo:</label>
                        <input type="text" id="nombre_completo" name="nombre_completo" v-model="nombre_completo" required>
                    </div>

                    <div class="form-group">
                        <label for="plan_estudio">Plan de Estudio:</label>
                        <select id="plan_estudio" v-model="plan" required>
                            <option value="2016">Plan 2009/2010 a partir de 2016</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="modalidad_titulacion">Modalidad de Titulación:</label>
                        <select id="modalidad_titulacion" name="modalidad_titulacion" v-model="modalidad" required>
                            <option value="">Seleccione una modalidad</option>
                            <option v-for="mod in modalidadesFiltradas" :value="mod">{{ mod }}</option>
                        </select>
                    </div>


                    <div class="form-group">
  <label for="fecha_inicio">Fecha de Inicio:</label>
  <input type="date" id="fecha_inicio" name="fecha_inicio" required>
</div>

<div class="form-group">
  <label for="fecha_termino">Fecha de Término:</label>
  <input type="date" id="fecha_termino" name="fecha_termino" readonly>
</div>

                    <button type="submit" class="btn">Registrar Alumno</button>
                </form>
            </div>
        </div>
    </div>

    <!-- VUE -->
    <script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
    <script>
        const { createApp } = Vue;

        createApp({
            data() {
                return {
                    numero_control: '',
                    nombre_completo: '',
                    plan: '',
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
                };
            },
            computed: {
                modalidadesFiltradas() {
                    return this.modalidades[this.plan] || [];
                }
            }
        }).mount('#app');
    </script>



<script>
  // Calcula automáticamente la fecha de término al seleccionar la de inicio
  document.getElementById('fecha_inicio').addEventListener('change', function () {
    const inicio = new Date(this.value);
    if (!isNaN(inicio)) {
      inicio.setFullYear(inicio.getFullYear() + 1);
      document.getElementById('fecha_termino').value = inicio.toISOString().split('T')[0];
    }
  });
</script>
</body>
</html>

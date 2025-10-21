<?php
@ini_set("display_errors", "1");
error_reporting(E_ALL);
try {
    require_once("../../../../include/dbcommon.php");
} catch (Exception $e) {
    header("Content-Type: application/json");
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud (POST o GET)
$dato = json_decode(file_get_contents('php://input'), true);

// Preparar parámetros
$departamento = isset($dato['departamento']) && !empty($dato['departamento']) ? $dato['departamento'] : NULL;
$limite = isset($dato['limite']) ? intval($dato['limite']) : 8;
$pagina = isset($dato['pagina']) ? intval($dato['pagina']) : 0;
$orden = isset($dato['orden']) ? $dato['orden'] : 'fecha_inicio';
$direccion = isset($dato['direccion']) ? $dato['direccion'] : 'ASC';
$estado = isset($dato['estado']) ? $dato['estado'] : 'activo';

// Obtener ID del evento desde POST o GET
$id_evento = null;
if (isset($dato['id_evento'])) {
    $id_evento = intval($dato['id_evento']);
} elseif (isset($_GET['id'])) {
    $id_evento = intval($_GET['id']);
}

// Verificar que el campo de ordenamiento sea seguro
$orden_permitidos = ['fecha_inicio', 'nombre', 'precio_entrada', 'cupos_disponibles'];
if (!in_array($orden, $orden_permitidos)) {
    $orden = 'fecha_inicio';
}

// Verificar dirección
$direccion_permitida = ['ASC', 'DESC'];
if (!in_array($direccion, $direccion_permitida)) {
    $direccion = 'ASC';
}

// CONSULTA MODIFICADA: Agregar JOIN con registro_asistencia_evento para contar personas registradas
$query = "
SELECT 
    eventos.*, 
    municipio.municipio AS nombre_municipio,
    COALESCE(COUNT(registro_asistencia_evento.id_registro), 0) AS personas_registradas
FROM eventos
LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
LEFT JOIN registro_asistencia_evento ON eventos.id_evento = registro_asistencia_evento.id_evento
WHERE 1=1";

// Agregar filtro por id_evento si está definido
if ($id_evento !== null) {
    $query .= " AND eventos.id_evento = " . intval($id_evento);
}

// Agregar filtros
if ($departamento !== NULL) {
    $departamento = str_replace("'", "''", $departamento);
    $query .= " AND eventos.departamento = '" . $departamento . "'";
}
if ($estado !== NULL) {
    $estado = str_replace("'", "''", $estado);
    $query .= " AND eventos.estado = '" . $estado . "'";
}

// IMPORTANTE: Agrupar por evento para evitar duplicados
$query .= " GROUP BY eventos.id_evento";

// Agregar ordenamiento y paginación
$offset = $pagina * $limite;
$query .= " ORDER BY eventos.`$orden` $direccion LIMIT $limite OFFSET $offset";

// Ejecutar consulta
$result = $conn->query($query);
$eventos = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Agregar tiendas participantes vacías por ahora (las tablas no existen)
        $row['tiendas_participantes'] = [];
        
        $eventos[] = $row;
    }
}

// Consulta para el total de registros
$query_total = "
SELECT COUNT(DISTINCT eventos.id_evento) as total 
FROM eventos
LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
WHERE 1=1";

if ($departamento !== NULL) {
    $query_total .= " AND eventos.departamento = '" . $departamento . "'";
}
if ($estado !== NULL) {
    $query_total .= " AND eventos.estado = '" . $estado . "'";
}

$resultado_total = $conn->query($query_total);
$total = 0;
if ($resultado_total && $rowTotal = $resultado_total->fetch_assoc()) {
    $total = $rowTotal['total'];
}

// Devolver resultados
echo json_encode([
    'success' => true,
    'total' => $total,
    'eventos' => $eventos
]);
?>
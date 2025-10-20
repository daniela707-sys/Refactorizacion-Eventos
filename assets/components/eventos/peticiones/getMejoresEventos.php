<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud (opcional, para parámetros adicionales)
$data = json_decode(file_get_contents('php://input'), true);

// Preparar parámetros
$limite = isset($data['limite']) ? intval($data['limite']) : 10;
$departamento = isset($data['departamento']) && !empty($data['departamento']) ? $data['departamento'] : NULL;
$estado = isset($data['estado']) ? $data['estado'] : 'activo';
$eventos_pasados = isset($data['eventos_pasados']) ? filter_var($data['eventos_pasados'], FILTER_VALIDATE_BOOLEAN) : false;

// Obtener fecha y hora actual
$fecha_actual = date('Y-m-d H:i:s');

// Consulta base para obtener eventos más visitados con número de personas registradas
$query = "
    SELECT eventos.*, 
           municipio.municipio AS nombre_municipio,
           COALESCE(COUNT(registro_asistencia_evento.id_registro), 0) AS personas_registradas
    FROM eventos
    LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
    LEFT JOIN registro_asistencia_evento ON eventos.id_evento = registro_asistencia_evento.id_evento
    WHERE 1=1
";

if ($estado !== NULL) {
    // Escapar comillas simples para evitar SQL injection
    $estado = str_replace("'", "''", $estado);
    $query .= " AND eventos.estado = '" . $estado . "'";
}

if ($departamento !== NULL) {
    $departamento = str_replace("'", "''", $departamento);
    $query .= " AND eventos.departamento = '" . $departamento . "'";
}

// ✅ FILTRO CRÍTICO: Solo eventos que NO han pasado (por defecto)
// Para eventos populares, típicamente queremos mostrar solo los próximos
if (!$eventos_pasados) {
    $query .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin >= '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio >= '" . $fecha_actual . "'
        END
    )";
} else {
    // Si se solicitan eventos pasados
    $query .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin < '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio < '" . $fecha_actual . "'
        END
    )";
}

// Agrupar por evento para evitar duplicados
$query .= " GROUP BY eventos.id_evento";

// Ordenar por contador de visitas de mayor a menor
$query .= " ORDER BY eventos.contador_visitas DESC";

// Limitar a los X eventos más populares
$query .= " LIMIT " . $limite;

// Ejecutar consulta
$result = DB::Query($query);
$eventos = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        $eventos[] = $row;
    }
}

// ✅ CONSULTA TOTAL CON EL MISMO FILTRO DE FECHAS
$queryTotal = "SELECT COUNT(*) as total FROM eventos WHERE 1=1";

if ($estado !== NULL) {
    $queryTotal .= " AND estado = '" . $estado . "'";
}

if ($departamento !== NULL) {
    $queryTotal .= " AND departamento = '" . $departamento . "'";
}

// Aplicar el mismo filtro de fechas al conteo total
if (!$eventos_pasados) {
    $queryTotal .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin >= '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio >= '" . $fecha_actual . "'
        END
    )";
} else {
    $queryTotal .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin < '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio < '" . $fecha_actual . "'
        END
    )";
}

$resultTotal = DB::Query($queryTotal);
$total = 0;

if ($resultTotal && $rowTotal = $resultTotal->fetchAssoc()) {
    $total = $rowTotal['total'];
}

// Devolver resultados
echo json_encode([
    'success' => true,
    'total' => $total,
    'eventos' => $eventos,
    'debug' => [
        'query' => $query,
        'queryTotal' => $queryTotal,
        'fecha_actual' => $fecha_actual,
        'eventos_pasados' => $eventos_pasados,
        'departamento' => $departamento
    ]
]);
?>
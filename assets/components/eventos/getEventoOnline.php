<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Preparar parámetros
$categoria = isset($data['categoria']) ? intval($data['categoria']) : NULL;
$limite = isset($data['limite']) ? intval($data['limite']) : 6;
$pagina = isset($data['pagina']) ? intval($data['pagina']) : 0;
$orden = isset($data['orden']) ? $data['orden'] : 'fecha_inicio';
$direccion = isset($data['direccion']) ? $data['direccion'] : 'ASC';
$estado = isset($data['estado']) ? $data['estado'] : 'activo';
$tipo = isset($data['tipo']) ? $data['tipo'] : null;
$eventos_pasados = isset($data['eventos_pasados']) ? filter_var($data['eventos_pasados'], FILTER_VALIDATE_BOOLEAN) : false;

// Obtener fecha y hora actual
$fecha_actual = date('Y-m-d H:i:s');

// Consulta base con conteo de personas registradas
$query = "
    SELECT eventos.*, 
           municipio.municipio AS nombre_municipio,
           COALESCE(COUNT(registro_asistencia_evento.id_registro), 0) AS personas_registradas
    FROM eventos
    LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
    LEFT JOIN registro_asistencia_evento ON eventos.id_evento = registro_asistencia_evento.id_evento
    WHERE 1=1
";

if ($tipo !== NULL) {
    $tipo = str_replace("'", "''", $tipo);
    $query .= " AND eventos.tipo = '" . $tipo . "'";
}

// Agregar filtros
if ($categoria !== NULL) {
    $query .= " AND eventos.categoria = " . $categoria;
}

if ($estado !== NULL) {
    // Escapar comillas simples para evitar SQL injection
    $estado = str_replace("'", "''", $estado);
    $query .= " AND eventos.estado = '" . $estado . "'";
}

// ✅ FILTRO CRÍTICO: Solo eventos que NO han pasado
// Usamos COALESCE para manejar casos donde fecha_fin es NULL
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

// Verificar que el campo de ordenamiento sea seguro
$ordenPermitidos = ['fecha_inicio', 'nombre', 'precio_entrada', 'cupos_disponibles', 'contador_visitas'];
if (!in_array($orden, $ordenPermitidos)) {
    $orden = 'fecha_inicio'; // Valor por defecto si no es válido
}

// Verificar dirección
$direccionPermitida = ['ASC', 'DESC'];
if (!in_array($direccion, $direccionPermitida)) {
    $direccion = 'ASC'; // Valor por defecto
}

// Agregar ordenamiento
$query .= " ORDER BY eventos.`" . $orden . "` " . $direccion;

// Agregar límite para paginación
$offset = $pagina * $limite;
$query .= " LIMIT " . $limite . " OFFSET " . $offset;

// Ejecutar consulta
$result = DB::Query($query);
$eventos = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        $eventos[] = $row;
    }
}

// ✅ QUERY TOTAL CON EL MISMO FILTRO DE FECHAS
$queryTotal = "SELECT COUNT(DISTINCT eventos.id_evento) as total FROM eventos WHERE 1=1";

if ($tipo !== NULL) {
    $queryTotal .= " AND tipo = '" . $tipo . "'";
}

if ($estado !== NULL) {
    $queryTotal .= " AND estado = '" . $estado . "'";
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
        'categoria' => $categoria,
        'fecha_actual' => $fecha_actual,
        'eventos_pasados' => $eventos_pasados
    ]
]);
?>
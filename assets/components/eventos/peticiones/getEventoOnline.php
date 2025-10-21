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
           COALESCE(m.municipio, 'Sin municipio') AS nombre_municipio,
           COALESCE(COUNT(r.id_registro), 0) AS personas_registradas
    FROM eventos
    LEFT JOIN municipio m ON eventos.municipio = m.idmuni
    LEFT JOIN registro_asistencia_evento r ON eventos.id_evento = r.id_evento
    WHERE 1=1
";

// Filtro por tipo (online/presencial)
if ($tipo !== NULL) {
    $tipo = str_replace("'", "''", $tipo);
    $query .= " AND eventos.tipo = '" . $tipo . "'";
} else {
    // Si no se especifica tipo, por defecto buscar eventos online
    $query .= " AND eventos.tipo = 'virtual'";
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

// ✅ FILTRO DE FECHAS TEMPORAL - Mostrar todos los eventos online para prueba
// Comentado temporalmente para mostrar eventos
/*
if (!$eventos_pasados) {
    $query .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin >= '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio >= '" . $fecha_actual . "'
        END
    )";
}
*/

// Agrupar por evento para evitar duplicados
$query .= " GROUP BY eventos.id_evento";

// Verificar que el campo de ordenamiento sea seguro
$orden_permitidos = ['fecha_inicio', 'nombre', 'precio_entrada', 'cupos_disponibles', 'contador_visitas'];
if (!in_array($orden, $orden_permitidos)) {
    $orden = 'fecha_inicio'; // Valor por defecto si no es válido
}

// Verificar dirección
$direccion_permitida = ['ASC', 'DESC'];
if (!in_array($direccion, $direccion_permitida)) {
    $direccion = 'ASC'; // Valor por defecto
}

// Agregar ordenamiento
$query .= " ORDER BY eventos.`" . $orden . "` " . $direccion;

// Agregar límite para paginación
$offset = $pagina * $limite;
$query .= " LIMIT " . $limite . " OFFSET " . $offset;

// Ejecutar consulta
$result = $conn->query($query);
$eventos = array();

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
}

// ✅ QUERY TOTAL CON EL MISMO FILTRO DE FECHAS
$queryTotal = "SELECT COUNT(DISTINCT eventos.id_evento) as total FROM eventos WHERE 1=1";

if ($tipo !== NULL) {
    $queryTotal .= " AND tipo = '" . $tipo . "'";
} else {
    // Si no se especifica tipo, por defecto buscar eventos online
    $queryTotal .= " AND tipo = 'virtual'";
}

if ($estado !== NULL) {
    $queryTotal .= " AND estado = '" . $estado . "'";
}

// Aplicar el mismo filtro de fechas al conteo total - COMENTADO TEMPORALMENTE
/*
if (!$eventos_pasados) {
    $queryTotal .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != '' 
            THEN eventos.fecha_fin >= '" . $fecha_actual . "'
            ELSE eventos.fecha_inicio >= '" . $fecha_actual . "'
        END
    )";
}
*/

$resultado_total = $conn->query($queryTotal);
$total = 0;

if ($resultado_total && $row_total = $resultado_total->fetch_assoc()) {
    $total = $row_total['total'];
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
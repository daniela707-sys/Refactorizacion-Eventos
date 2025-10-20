<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud
$data = json_decode(file_get_contents('php://input'), true);

// Preparar parámetros
$departamento = isset($data['departamento']) && !empty($data['departamento']) ? $data['departamento'] : NULL;
$limite = isset($data['limite']) ? intval($data['limite']) : 8;
$pagina = isset($data['pagina']) ? intval($data['pagina']) : 0;
$orden = isset($data['orden']) ? $data['orden'] : 'fecha_inicio';
$direccion = isset($data['direccion']) ? $data['direccion'] : 'ASC';
$estado = isset($data['estado']) ? $data['estado'] : 'activo';
$categoria = isset($data['categoria']) ? $data['categoria'] : NULL;
$buscar = isset($data['buscar']) ? $data['buscar'] : NULL;

// ✅ NUEVO: Parámetro para controlar eventos pasados/futuros
$mostrarEventosPasados = isset($data['eventos_pasados']) ? boolval($data['eventos_pasados']) : false;

// Verificar que el campo de ordenamiento sea seguro
$ordenPermitidos = ['fecha_inicio', 'nombre', 'precio_entrada', 'cupos_disponibles', 'fecha_fin'];
if (!in_array($orden, $ordenPermitidos)) {
    $orden = 'fecha_inicio'; // Valor por defecto si no es válido
}

// Verificar dirección
$direccionPermitida = ['ASC', 'DESC'];
if (!in_array($direccion, $direccionPermitida)) {
    $direccion = 'ASC'; // Valor por defecto
}

// Consulta base con JOIN para traer el nombre del municipio y contar registros
$query = "
SELECT 
    eventos.*, 
    municipio.municipio AS nombre_municipio,
    COALESCE(COUNT(registro_asistencia_evento.id_registro), 0) AS personas_registradas
FROM eventos
LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
LEFT JOIN registro_asistencia_evento ON eventos.id_evento = registro_asistencia_evento.id_evento
WHERE 1=1";

// ✅ NUEVO: Filtro de fechas - CLAVE PARA LA OPTIMIZACIÓN
if ($mostrarEventosPasados) {
    // Mostrar solo eventos que YA terminaron
    $query .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != eventos.fecha_inicio 
            THEN eventos.fecha_fin < NOW()
            ELSE eventos.fecha_inicio < NOW()
        END
    )";
    // Para eventos pasados, ordenar por fecha descendente (más recientes primero)
    if ($orden === 'fecha_inicio') {
        $direccion = 'DESC';
    }
} else {
    // Mostrar solo eventos que NO han terminado (comportamiento por defecto)
    $query .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != eventos.fecha_inicio 
            THEN eventos.fecha_fin >= NOW()
            ELSE eventos.fecha_inicio >= NOW()
        END
    )";
}

// Agregar filtros existentes
if ($departamento !== NULL && $departamento !== 'TODOS') {
    $departamento = str_replace("'", "''", $departamento);
    $query .= " AND eventos.departamento = '" . $departamento . "'";
}
if ($estado !== NULL) {
    $estado = str_replace("'", "''", $estado);
    $query .= " AND eventos.estado = '" . $estado . "'";
}
if ($categoria !== NULL) {
    $query .= " AND eventos.categoria = " . intval($categoria);
}
if ($buscar !== NULL) {
    $buscar = str_replace("'", "''", $buscar);
    $query .= " AND (eventos.nombre LIKE '%" . $buscar . "%' OR eventos.descripcion LIKE '%" . $buscar . "%')";
}

// Agrupar por evento para evitar duplicados
$query .= " GROUP BY eventos.id_evento";

// Agregar ordenamiento y paginación
$offset = $pagina * $limite;
$query .= " ORDER BY eventos.`$orden` $direccion LIMIT $limite OFFSET $offset";

// Ejecutar consulta
$result = DB::Query($query);
$eventos = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        $eventos[] = $row;
    }
}

// ✅ CONSULTA PARA EL TOTAL CON EL MISMO FILTRO DE FECHAS
$queryTotal = "
SELECT COUNT(DISTINCT eventos.id_evento) as total 
FROM eventos
LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
WHERE 1=1";

// Aplicar el mismo filtro de fechas para el conteo
if ($mostrarEventosPasados) {
    $queryTotal .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != eventos.fecha_inicio 
            THEN eventos.fecha_fin < NOW()
            ELSE eventos.fecha_inicio < NOW()
        END
    )";
} else {
    $queryTotal .= " AND (
        CASE 
            WHEN eventos.fecha_fin IS NOT NULL AND eventos.fecha_fin != eventos.fecha_inicio 
            THEN eventos.fecha_fin >= NOW()
            ELSE eventos.fecha_inicio >= NOW()
        END
    )";
}

if ($departamento !== NULL && $departamento !== 'TODOS') {
    $queryTotal .= " AND eventos.departamento = '" . $departamento . "'";
}
if ($estado !== NULL) {
    $queryTotal .= " AND eventos.estado = '" . $estado . "'";
}
if ($categoria !== NULL) {
    $queryTotal .= " AND eventos.categoria = " . intval($categoria);
}
if ($buscar !== NULL) {
    $queryTotal .= " AND (eventos.nombre LIKE '%" . $buscar . "%' OR eventos.descripcion LIKE '%" . $buscar . "%')";
}

$resultTotal = DB::Query($queryTotal);
$total = 0;
if ($resultTotal && $rowTotal = $resultTotal->fetchAssoc()) {
    $total = $rowTotal['total'];
}

// ✅ CONSULTA ADICIONAL: Contar eventos del tipo contrario para mostrar información útil
$queryContrario = str_replace(
    $mostrarEventosPasados ? "< NOW()" : ">= NOW()",
    $mostrarEventosPasados ? ">= NOW()" : "< NOW()",
    $queryTotal
);

$resultContrario = DB::Query($queryContrario);
$totalContrario = 0;
if ($resultContrario && $rowContrario = $resultContrario->fetchAssoc()) {
    $totalContrario = $rowContrario['total'];
}

// Devolver resultados
echo json_encode([
    'success' => true,
    'total' => $total,
    'eventos' => $eventos,
    'total_contrario' => $totalContrario, // Para saber si hay eventos del otro tipo
    'eventos_pasados' => $mostrarEventosPasados,
    'debug' => [
        'query' => $query,
        'countQuery' => $queryTotal,
        'categoria' => $categoria,
        'mostrar_pasados' => $mostrarEventosPasados
    ]
]);
?>
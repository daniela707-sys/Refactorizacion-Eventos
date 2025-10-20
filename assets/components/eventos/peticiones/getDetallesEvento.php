<?php
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Obtener los datos de la solicitud
$dato = json_decode(file_get_contents('php://input'), true);

// Preparar parámetros
$departamento = isset($dato['departamento']) && !empty($dato['departamento']) ? $dato['departamento'] : NULL;
$limite = isset($dato['limite']) ? intval($dato['limite']) : 8;
$pagina = isset($dato['pagina']) ? intval($dato['pagina']) : 0;
$orden = isset($dato['orden']) ? $dato['orden'] : 'fecha_inicio';
$direccion = isset($dato['direccion']) ? $dato['direccion'] : 'ASC';
$estado = isset($dato['estado']) ? $dato['estado'] : 'activo';
$id_evento = isset($dato['id_evento']) ? intval($dato['id_evento']) : null;

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
$result = DB::Query($query);
$eventos = array();
$debug_info = array();

if ($result) {
    while ($row = $result->fetchAssoc()) {
        // Consultar las tiendas participantes SOLO si se solicita un evento específico
        if ($id_evento !== null) {
            $tiendas = array();
            
            // EMPRENDEDORES: Consulta con tabla 'tienda' y campos correctos
            $query_emprendedores = "
            SELECT 
                et.*,
                t.nombre AS tienda_nombre,
                t.logo AS logo,
                t.telefono AS tienda_contacto,
                t.pagina_web AS tienda_website
            FROM 
                evento_tiendas et
            LEFT JOIN 
                tienda t ON et.tienda_id = t.id_negocio
            WHERE 
                et.evento_id = " . intval($id_evento) . "
                AND et.estado = 'activo'
                AND et.tipo_tienda = 'emprendedor'
            ORDER BY 
                t.nombre ASC";
                
            $debug_info['consulta_emprendedores'] = $query_emprendedores;
            $resultado_emprendedores = DB::Query($query_emprendedores);
            
            if ($resultado_emprendedores) {
                while ($emprendedor_row = $resultado_emprendedores->fetchAssoc()) {
                    $tiendas[] = $emprendedor_row;
                }
                $debug_info['emprendedores_exitoso'] = true;
                $debug_info['total_emprendedores'] = count($tiendas);
            } else {
                $debug_info['emprendedores_exitoso'] = false;
                $debug_info['error_emprendedores'] = DB::LastError();
            }
            
            // TIENDAS EXTERNAS: Consulta con tabla 'tiendas_externas' (sin telefono)
            $query_externas = "
            SELECT 
                et.*,
                te.nombre AS tienda_nombre,
                te.logo AS logo,
                '' AS tienda_contacto,
                '' AS tienda_website
            FROM 
                evento_tiendas et
            LEFT JOIN 
                tiendas_externas te ON et.tienda_id = te.id_tiendas_externas
            WHERE 
                et.evento_id = " . intval($id_evento) . "
                AND et.estado = 'activo'
                AND et.tipo_tienda = 'externa'
            ORDER BY 
                te.nombre ASC";
                
            $debug_info['consulta_externas'] = $query_externas;
            $resultados_externos = DB::Query($query_externas);
            
            if ($resultados_externos) {
                while ($externaRow = $resultados_externos->fetchAssoc()) {
                    $tiendas[] = $externaRow;
                }
                $debug_info['externas_exitoso'] = true;
                $debug_info['total_externas'] = count($tiendas) - ($debug_info['total_emprendedores'] ?? 0);
            } else {
                $debug_info['externas_exitoso'] = false;
                $debug_info['error_externas'] = DB::LastError();
            }
            
            $debug_info['total_tiendas_final'] = count($tiendas);
            $row['tiendas_participantes'] = $tiendas;
        } else {
            $row['tiendas_participantes'] = [];
        }
        
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

$resultado_total = DB::Query($query_total);
$total = 0;
if ($resultado_total && $rowTotal = $resultado_total->fetchAssoc()) {
    $total = $rowTotal['total'];
}

// Devolver resultados con debugging completo
echo json_encode([
    'success' => true,
    'total' => $total,
    'eventos' => $eventos,
    'debug' => [
        'query_principal' => $query,
        'id_evento' => $id_evento,
        'debug_tiendas' => $debug_info
    ]
]);
?>


<?php
/*
@ini_set("display_errors", "1");
require_once("../../../../include/dbcommon.php");
header("Access-Control-Allow-Origin: *"); 
header("Content-Type: application/json"); 
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Manejar solicitudes OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Obtener el ID del evento
$id_evento = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_evento <= 0) {
    echo json_encode([
        'success' => false,
        'mensaje' => 'ID de evento no válido'
    ]);
    exit;
}

// Consultar el evento
$query = "SELECT * FROM eventos WHERE id_evento = 2";
$params = [$id_evento];

$result = DB::Query($query, $params);
$evento = null;

if ($result && ($row = $result->fetchAssoc())) {
    // Formatear datos numéricos para JSON
    $row['id_evento'] = intval($row['id_evento']);
    $row['categoria'] = intval($row['categoria']);
    $row['subcategoria'] = $row['subcategoria'] ? intval($row['subcategoria']) : null;
    $row['aforo_maximo'] = intval($row['aforo_maximo']);
    $row['contador_visitas'] = intval($row['contador_visitas']);
    $row['precio_entrada'] = floatval($row['precio_entrada']);
    $row['es_gratuito'] = $row['es_gratuito'] == '1';
    $row['cupos_disponibles'] = intval($row['cupos_disponibles']);
    
    if ($row['calificacion'] !== null) {
        $row['calificacion'] = floatval($row['calificacion']);
    }
    
    $evento = $row;
}

// Devolver respuesta
echo json_encode([
    'success' => ($evento !== null),
    'evento' => $evento
]);
*/
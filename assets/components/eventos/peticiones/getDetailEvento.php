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
$id_evento = isset($data['id_evento']) ? intval($data['id_evento']) : null;

// Verificar que el campo de ordenamiento sea seguro
$ordenPermitidos = ['fecha_inicio', 'nombre', 'precio_entrada', 'cupos_disponibles'];
if (!in_array($orden, $ordenPermitidos)) {
    $orden = 'fecha_inicio';
}

// Verificar dirección
$direccionPermitida = ['ASC', 'DESC'];
if (!in_array($direccion, $direccionPermitida)) {
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
            $queryEmprendedores = "
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
                
            $debug_info['consulta_emprendedores'] = $queryEmprendedores;
            $resultEmprendedores = DB::Query($queryEmprendedores);
            
            if ($resultEmprendedores) {
                while ($emprendedorRow = $resultEmprendedores->fetchAssoc()) {
                    $tiendas[] = $emprendedorRow;
                }
                $debug_info['emprendedores_exitoso'] = true;
                $debug_info['total_emprendedores'] = count($tiendas);
            } else {
                $debug_info['emprendedores_exitoso'] = false;
                $debug_info['error_emprendedores'] = DB::LastError();
            }
            
            // TIENDAS EXTERNAS: Consulta con tabla 'tiendas_externas' (sin telefono)
            $queryExternas = "
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
                
            $debug_info['consulta_externas'] = $queryExternas;
            $resultExternas = DB::Query($queryExternas);
            
            if ($resultExternas) {
                while ($externaRow = $resultExternas->fetchAssoc()) {
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
$queryTotal = "
SELECT COUNT(DISTINCT eventos.id_evento) as total 
FROM eventos
LEFT JOIN municipio ON eventos.municipio = municipio.idmuni
WHERE 1=1";

if ($departamento !== NULL) {
    $queryTotal .= " AND eventos.departamento = '" . $departamento . "'";
}
if ($estado !== NULL) {
    $queryTotal .= " AND eventos.estado = '" . $estado . "'";
}

$resultTotal = DB::Query($queryTotal);
$total = 0;
if ($resultTotal && $rowTotal = $resultTotal->fetchAssoc()) {
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
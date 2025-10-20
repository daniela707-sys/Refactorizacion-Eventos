<?php
require_once("../../../../../../include/dbcommon.php");

class Comentario
{
    public static function create_comentario($comentario, $fecha_hora, $estrellas, $producto)
    {
        $correo = $_SESSION['UserID'];
        try {
            $query = "call sp_crear_comentario_producto ('$comentario', '$fecha_hora', $estrellas , $producto, '$correo')";
            $result = DB::Query($query);

            if ($result) {
                echo 'Comentario creado correctamente';
            } else {
                throw new Exception('Error en la ejecución de la consulta');
            }
        } catch (Exception $e) {
            echo 'Error al crear el comentario: ' . $e->getMessage();
        }
    }

    public static function delete_comentario_by_id($comentario_id)
    {
        try {
            $query = "DELETE FROM comentarios_producto WHERE id_comentarios_producto = $comentario_id";
            $result = DB::Query($query);

            if ($result) {
                echo 'Comentario eliminado correctamente';
            } else {
                throw new Exception('No se pudo borrar el comentario');
            }
        } catch (Exception $e) {
            echo 'Error al eliminar el comentario: ' . $e->getMessage();
        }
    }

    public static function get_all_comentarios_con_subcomentarios($id_producto)
    {
        try {
            $query = "
            SELECT 
            c.id_comentario_productos AS comentario_id, 
            c.fecha AS fecha_hora, 
            c.comentario, 
            c.estrellas, 
            c.producto, 
            c.usuario, 
            s.id_sub_comentarios AS subcomentario_id, 
            s.comentario AS subcomentario,
            u.correo_electronico AS usuario_correo,
            u.nombres AS usuario_nombres,
            u.apellidos AS usuario_apellidos,
            u.foto AS usuario_foto,
            su.nombres AS subcomentario_usuario_nombres,
            su.apellidos AS subcomentario_usuario_apellidos
            FROM comentarios_productos c
            LEFT JOIN sub_comentarios_producto s ON c.id_comentario_productos = s.id_comentario AND (s.estado IS NULL OR s.estado = 1)
            LEFT JOIN informacion_usuario u ON c.usuario = u.id_usuario
            LEFT JOIN informacion_usuario su ON s.usuario = su.id_usuario
            WHERE c.producto = $id_producto AND c.estado = 1
            ORDER BY c.fecha DESC, s.id_sub_comentarios ASC";

            $result = DB::Query($query);

            if ($result) {
                $results = array();
                while ($row = $result->fetchAssoc()) {
                    $results[] = $row;
                }

                // Organizar comentarios y subcomentarios en una estructura jerárquica
                $comentarios = [];
                foreach ($results as $row) {
                    $comentario_id = $row['comentario_id'];
                    if (!isset($comentarios[$comentario_id])) {
                        $comentarios[$comentario_id] = [
                            'comentario_id' => $row['comentario_id'],
                            'fecha_hora' => $row['fecha_hora'],
                            'comentario' => $row['comentario'],
                            'estrellas' => $row['estrellas'],
                            'producto' => $row['producto'],
                            'usuario' => $row['usuario'],
                            'usuario_correo' => $row['usuario_correo'],
                            'usuario_nombres' => $row['usuario_nombres'],
                            'usuario_apellidos' => $row['usuario_apellidos'],
                            'usuario_foto' => $row['usuario_foto'],
                            'subcomentarios' => []
                        ];
                    }
                    if ($row['subcomentario_id'] !== null) {
                        $comentarios[$comentario_id]['subcomentarios'][] = [
                            'subcomentario_id' => $row['subcomentario_id'],
                            'subcomentario' => $row['subcomentario'],
                            'subcomentario_usuario_nombres' => $row['subcomentario_usuario_nombres'],
                            'subcomentario_usuario_apellidos' => $row['subcomentario_usuario_apellidos']
                        ];
                    }
                }

                return array_values($comentarios); // Convertir a array indexado
            } else {
                throw new Exception('No se encontraron comentarios.');
            }
        } catch (Exception $e) {
            echo 'Error al obtener los comentarios: ' . $e->getMessage();
            return [];
        }
    }

    public static function update_comentario($comentario_id, $comentario, $fecha_hora, $estrellas)
    {
        try {
            $query = "UPDATE comentarios_producto SET comentario='$comentario', fecha='$fecha_hora', estrellas=$estrellas WHERE id_comentarios_producto=$comentario_id";
            $result = DB::Query($query);

            if ($result) {
                echo 'Comentario actualizado correctamente';
            } else {
                throw new Exception('No se pudo actualizar el comentario');
            }
        } catch (Exception $e) {
            echo 'Error al actualizar el comentario: ' . $e->getMessage();
        }
    }

    public static function create_subcomentario($comentario_id, $subcomentario)
    {
        $correo = $_SESSION['UserID'];
        try {
            $query = "call sp_crear_subcomentario_producto($comentario_id, '$subcomentario', '$correo')";
            $result = DB::Query($query);

            if ($result) {
                return ['success' => true];
            } else {
                return ['success' => false, 'message' => 'Error al insertar el subcomentario'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
}

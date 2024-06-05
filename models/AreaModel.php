<?php

require_once 'database/conexion.php';

class AreaModel {
    public function QueryAllModel() {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM area WHERE activo = true ORDER BY id_area");
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'message' => "Error al obtener todas las áreas: " . $e->getMessage()];
        }
    }

    public function QueryOneModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("SELECT * FROM area WHERE id_area = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return ['res' => true, 'data' => $stmt->fetch(PDO::FETCH_ASSOC)];
        } catch (PDOException $e) {
            return ['res' => false, 'message' => "Error al obtener el área con ID $id: " . $e->getMessage()];
        }
    }

    public function InsertModel($datos) {
        try {
            $conn = Conexion::Conexion();

            // Verificar si ya existe un área con el mismo nombre (sin importar mayúsculas o minúsculas)
            $stmt = $conn->prepare("SELECT COUNT(*) FROM area WHERE LOWER(nombre_area) = LOWER(:nombreArea)");
            $stmt->bindParam(':nombreArea', $datos['nombreArea'], PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                return ['res' => false, 'message' => "El área con el nombre '{$datos['nombreArea']}' ya existe"];
            }

            // Insertar el nuevo área
            $stmt = $conn->prepare("INSERT INTO area (nombre_area, fecha_creacion, hora_creacion, fecha_actualizado) VALUES (:nombreArea, :fecha_creacion, :hora_creacion, :fecha_actualizado)");
            $stmt->bindParam(':nombreArea', $datos['nombreArea'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_creacion', $datos['fecha_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':hora_creacion', $datos['hora_creacion'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();

            return ['res' => true, 'message' => "Área insertada exitosamente"];
        } catch (PDOException $e) {
            throw new Exception("Error al insertar el área: " . $e->getMessage());
        }
    }            

    public function UpdateModel($id, $datos) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE area SET nombre_area = :nombre_area, fecha_actualizado = :fecha_actualizado WHERE id_area = :id AND activo = true");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nombre_area', $datos['nombre_area'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_actualizado', $datos['fecha_actualizado'], PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'message' => "No se encontró un área activa con ID $id"];
            }
            return ['res' => true, 'message' => "Área con ID $id actualizada exitosamente"];
        } catch (PDOException $e) {
            return ['res' => false, 'message' => "Error al actualizar el área: " . $e->getMessage()];
        }
    }

    public function DeleteModel($id) {
        try {
            $conn = Conexion::Conexion();
            $stmt = $conn->prepare("UPDATE area SET activo = false WHERE id_area = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return ['res' => false, 'message' => "No se encontró un área activa con ID $id"];
            }
            return ['res' => true, 'message' => "Área con ID $id desactivada exitosamente"];
        } catch (PDOException $e) {
            return ['res' => false, 'message' => "Error al desactivar el área: " . $e->getMessage()];
        }
    }
}
    
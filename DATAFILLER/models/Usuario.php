<?php
namespace App\Models;

use PDO;
use PDOException;

class Usuario {
    private $conn;
    private $table = 'tbusuario';
    
    // Propiedades del usuario
    public $id;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $email;
    public $password;
    public $tipo_plan;
    public $consultas_diarias;
    
    // Constructor con conexión DB
    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo usuario
    public function crear() {
        // Verificar si el usuario ya existe
        if($this->buscarPorNombre($this->nombre)) {
            return false; // Usuario ya existe
        }
        
        // Query para insertar
        $query = "INSERT INTO " . $this->table . " 
                  SET nombre = :nombre, 
                      apellido_paterno = :apellido_paterno, 
                      apellido_materno = :apellido_materno,
                      email = :email,
                      password = :password,
                      tipo_plan = 'gratuito'";
        
        // Preparar statement
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags(strtolower($this->nombre)));
        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        $this->email = htmlspecialchars(strip_tags(strtolower($this->email)));
        
        // Hash de la contraseña
        $hash_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Vincular valores
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido_paterno', $this->apellido_paterno);
        $stmt->bindParam(':apellido_materno', $this->apellido_materno);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $hash_password);
        
        // Ejecutar query
        if($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        
        // Si algo sale mal
        return false;
    }
    
    // Buscar un usuario por nombre
    public function buscarPorNombre($nombre) {
        try {
            // Query para buscar usuario por nombre
            $query = "SELECT * FROM " . $this->table . " WHERE nombre = :nombre LIMIT 1";
            
            // Preparar la consulta
            $stmt = $this->conn->prepare($query);
            
            // Sanitizar datos
            $nombre = htmlspecialchars(strip_tags(strtolower($nombre)));
            
            // Vincular parámetros
            $stmt->bindParam(':nombre', $nombre);
            
            // Ejecutar la consulta
            $stmt->execute();
            
            // Verificar si se encontró el usuario
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error en buscar usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Buscar un usuario por email
    public function buscarPorEmail($email) {
        try {
            $query = "SELECT id, nombre, apellido_paterno, email FROM " . $this->table . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error buscando por email: " . $e->getMessage());
            return false;
        }
    }
    // Validar el inicio de sesión
    public function validarLogin($nombre, $password) {
        try {
            $usuario = $this->buscarPorNombre($nombre);
            
            if($usuario && password_verify($password, $usuario['password'])) {
                return [
                    'exito' => true,
                    'usuario' => [
                        'id' => $usuario['id'], // ¡IMPORTANTE! Incluir el ID
                        'nombre' => $usuario['nombre'],
                        'apellido_paterno' => $usuario['apellido_paterno'],
                        'email' => $usuario['email']
                    ]
                ];
            }
            
            return ['exito' => false];
        } catch(PDOException $e) {
            error_log("Error en validar login: " . $e->getMessage());
            return ['exito' => false];
        }
    }
    
    // Método para verificar límites de consultas
    public function puedeRealizarConsulta($usuario_id) {
        try {
            $query = "SELECT tipo_plan, consultas_diarias, fecha_ultima_consulta FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Si es premium, puede hacer consultas ilimitadas
                if($usuario['tipo_plan'] === 'premium') {
                    return true;
                }
                
                // Para usuarios gratuitos, verificar límite diario
                $hoy = date('Y-m-d');
                if($usuario['fecha_ultima_consulta'] !== $hoy) {
                    // Resetear contador si es un nuevo día
                    $this->resetearConsultasDiarias($usuario_id);
                    return true;
                }
                
                return $usuario['consultas_diarias'] < 3;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Error verificando consultas: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para incrementar contador de consultas
    public function incrementarConsultas($usuario_id) {
        try {
            $hoy = date('Y-m-d');
            
            // Verificar si ya se hizo consulta hoy
            $query = "SELECT consultas_diarias, fecha_ultima_consulta FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $datos = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($datos['fecha_ultima_consulta'] === $hoy) {
                    // Mismo día, incrementar contador
                    $nuevas_consultas = $datos['consultas_diarias'] + 1;
                } else {
                    // Nuevo día, resetear contador
                    $nuevas_consultas = 1;
                }
                
                // Actualizar contador y fecha
                $query_update = "UPDATE " . $this->table . " 
                                SET consultas_diarias = :consultas, fecha_ultima_consulta = :fecha 
                                WHERE id = :id";
                $stmt_update = $this->conn->prepare($query_update);
                $stmt_update->bindParam(':consultas', $nuevas_consultas);
                $stmt_update->bindParam(':fecha', $hoy);
                $stmt_update->bindParam(':id', $usuario_id);
                
                return $stmt_update->execute();
            }
            
            return false;
        } catch(PDOException $e) {
            error_log("Error incrementando consultas: " . $e->getMessage());
            return false;
        }
    }
    
    // Método para resetear consultas diarias
    private function resetearConsultasDiarias($usuario_id) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET consultas_diarias = 0, 
                          fecha_ultima_consulta = CURDATE() 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error reseteando consultas: " . $e->getMessage());
            return false;
        }
    }
    
    // Obtener información completa del usuario
    public function obtenerInfoCompleta($usuario_id) {
        try {
            $query = "SELECT id, nombre, apellido_paterno, email, tipo_plan, consultas_diarias, fecha_ultima_consulta 
                      FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error obteniendo info usuario: " . $e->getMessage());
            return false;
        }
    }
    
    // Función que falta: obtenerInfoUsuario
    public function obtenerInfoUsuario($usuario_id) {
        try {
            $query = "SELECT id, nombre, apellido_paterno, email, tipo_plan as plan, consultas_diarias, fecha_ultima_consulta 
                      FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error obteniendo info usuario: " . $e->getMessage());
            return false;
        }
    }

    // Función que falta: obtenerConsultasHoy
    public function obtenerConsultasHoy($usuario_id) {
        try {
            $query = "SELECT consultas_diarias 
                      FROM " . $this->table . " 
                      WHERE id = :id AND fecha_ultima_consulta = CURDATE()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['consultas_diarias'];
            }
            
            // Si no hay registros para hoy, retornar 0
            return 0;
        } catch(PDOException $e) {
            error_log("Error obteniendo consultas hoy: " . $e->getMessage());
            return 0;
        }
    }

    // Función auxiliar para obtener el plan del usuario
    /*
    public function obtenerPlanUsuario($usuario_id) {
        try {
            $query = "SELECT tipo_plan FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                return $resultado['tipo_plan'];
            }
            return 'gratuito'; // Por defecto
        } catch(PDOException $e) {
            error_log("Error obteniendo plan: " . $e->getMessage());
            return 'gratuito';
        }
    }
    */
    // Función auxiliar para obtener consultas restantes
    public function obtenerConsultasRestantes($usuario_id) {
        try {
            $info_usuario = $this->obtenerInfoUsuario($usuario_id);
            
            if(!$info_usuario) return 0;
            
            // Si es premium, consultas ilimitadas
            if($info_usuario['plan'] === 'premium') {
                return 999; // Número grande para representar "ilimitado"
            }
            
            // Para usuarios gratuitos
            $hoy = date('Y-m-d');
            if($info_usuario['fecha_ultima_consulta'] === $hoy) {
                return max(0, 3 - $info_usuario['consultas_diarias']);
            } else {
                return 3; // Nuevo día, consultas completas
            }
        } catch(PDOException $e) {
            error_log("Error calculando consultas restantes: " . $e->getMessage());
            return 0;
        }
    }

    // Función para actualizar el plan de un usuario
    public function actualizarPlan($usuario_id, $nuevo_plan) {
        try {
            $query = "UPDATE " . $this->table . " 
                      SET tipo_plan = :plan 
                      WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':plan', $nuevo_plan);
            $stmt->bindParam(':id', $usuario_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error actualizando plan: " . $e->getMessage());
            return false;
        }
    }
    /*
    // Función para obtener estadísticas del usuario
    public function obtenerEstadisticasUsuario($usuario_id) {
        try {
            // Consultar auditoría de consultas
            $query = "SELECT 
                        COUNT(*) as total_consultas,
                        SUM(cantidad_registros) as total_registros_generados,
                        COUNT(DISTINCT DATE(fecha_consulta)) as dias_activos,
                        MAX(fecha_consulta) as ultima_actividad
                      FROM tbauditoria_consultas 
                      WHERE usuario_id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return [
                'total_consultas' => 0,
                'total_registros_generados' => 0,
                'dias_activos' => 0,
                'ultima_actividad' => null
            ];
        } catch(PDOException $e) {
            error_log("Error obteniendo estadísticas: " . $e->getMessage());
            return [
                'total_consultas' => 0,
                'total_registros_generados' => 0,
                'dias_activos' => 0,
                'ultima_actividad' => null
            ];
        }
    }
*/
    // Función para validar si el usuario existe
    public function existeUsuario($usuario_id) {
        try {
            $query = "SELECT id FROM " . $this->table . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $usuario_id);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch(PDOException $e) {
            error_log("Error verificando existencia usuario: " . $e->getMessage());
            return false;
        }
    }

    // Función para limpiar tokens expirados (mantenimiento)
    public function limpiarTokensExpirados() {
        try {
            $query = "DELETE FROM tbrecuperacion_password WHERE fecha_expiracion < NOW()";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error limpiando tokens: " . $e->getMessage());
            return false;
        }
    }

    // Guardar token de recuperación
    public function guardarTokenRecuperacion($usuario_id, $token, $expiracion) {
        try {
            // Primero eliminar tokens anteriores del usuario
            $query = "DELETE FROM tbrecuperacion_password WHERE usuario_id = :usuario_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->execute();
            
            // Insertar nuevo token
            $query = "INSERT INTO tbrecuperacion_password (usuario_id, token, fecha_expiracion) 
                      VALUES (:usuario_id, :token, :expiracion)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':usuario_id', $usuario_id);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':expiracion', $expiracion);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error guardando token: " . $e->getMessage());
            return false;
        }
    }
    
    // Verificar token de recuperación
    public function verificarTokenRecuperacion($token) {
        try {
            $query = "SELECT tr.*, tu.email, tu.nombre 
                      FROM tbrecuperacion_password tr 
                      JOIN tbusuario tu ON tr.usuario_id = tu.id 
                      WHERE tr.token = :token AND tr.fecha_expiracion > NOW() AND tr.usado = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error verificando token: " . $e->getMessage());
            return false;
        }
    }
    
    // Cambiar contraseña
    public function cambiarPassword($usuario_id, $nueva_password) {
        try {
            $password_hash = password_hash($nueva_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':password', $password_hash);
            $stmt->bindParam(':id', $usuario_id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error cambiando password: " . $e->getMessage());
            return false;
        }
    }
    
    // Marcar token como usado
    public function marcarTokenUsado($token) {
        try {
            $query = "UPDATE tbrecuperacion_password SET usado = 1 WHERE token = :token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error marcando token usado: " . $e->getMessage());
            return false;
        }
    }

    // Agregar este método que falta
    public function calcularConsultasRestantes($usuario_id) {
        try {
            $info_usuario = $this->obtenerInfoUsuario($usuario_id);
            
            if(!$info_usuario) return 0;
            
            // Si es premium, consultas ilimitadas
            if($info_usuario['plan'] === 'premium') {
                return 999; // Número grande para representar "ilimitado"
            }
            
            // Para usuarios gratuitos
            $hoy = date('Y-m-d');
            if($info_usuario['fecha_ultima_consulta'] === $hoy) {
                return max(0, 3 - $info_usuario['consultas_diarias']);
            } else {
                return 3; // Nuevo día, consultas completas
            }
        } catch(PDOException $e) {
            error_log("Error calculando consultas restantes: " . $e->getMessage());
            return 0;
        }
    }
}
?>
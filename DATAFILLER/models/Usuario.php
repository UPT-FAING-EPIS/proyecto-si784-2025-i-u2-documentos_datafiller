<?php
require_once 'config/database.php';

class Usuario {
    private $conn;
    private $table = 'tbusuario';
    
    // Propiedades del usuario
    public $id;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $password;
    
    // Constructor con conexión DB
    public function __construct($db) {
        $this->conn = $db;
    }
    
    // Crear nuevo usuario
    public function crear() {
        // Query para insertar
        $query = "INSERT INTO " . $this->table . " 
                  SET nombre = :nombre, 
                      apellido_paterno = :apellido_paterno, 
                      apellido_materno = :apellido_materno,
                      password = :password";
        
        // Preparar statement
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
        $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
        
        // Hash de la contraseña
        $hash_password = password_hash($this->password, PASSWORD_DEFAULT);
        
        // Vincular valores
        $stmt->bindParam(':nombre', $this->nombre);
        $stmt->bindParam(':apellido_paterno', $this->apellido_paterno);
        $stmt->bindParam(':apellido_materno', $this->apellido_materno);
        $stmt->bindParam(':password', $hash_password);
        
        // Ejecutar query
        if($stmt->execute()) {
            return true;
        }
        
        // Si algo sale mal
        printf("Error: %s.\n", $stmt->error);
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
    
    // Validar el inicio de sesión
    public function validarLogin($nombre, $password) {
        try {
            // Buscar usuario por nombre
            $usuario = $this->buscarPorNombre($nombre);
            
            // Si el usuario existe y la contraseña es correcta
            if($usuario && password_verify($password, $usuario['password'])) {
                return [
                    'exito' => true,
                    'usuario' => [
                        'id' => $usuario['id'],
                        'nombre' => $usuario['nombre'],
                        'apellido_paterno' => $usuario['apellido_paterno']
                    ]
                ];
            }
            
            return ['exito' => false];
        } catch(PDOException $e) {
            error_log("Error en validar login: " . $e->getMessage());
            return ['exito' => false];
        }
    }
}
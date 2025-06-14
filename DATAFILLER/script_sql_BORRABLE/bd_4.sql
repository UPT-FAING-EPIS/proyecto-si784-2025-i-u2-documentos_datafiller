-- Crear base de datos
IF NOT EXISTS (SELECT name FROM sys.databases WHERE name = 'datafiller')
BEGIN
    CREATE DATABASE datafiller;
END
GO

USE datafiller;
GO

-- Tabla: tbusuario
CREATE TABLE tbusuario (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL UNIQUE,
    apellido_paterno NVARCHAR(50) NOT NULL,
    apellido_materno NVARCHAR(50) NOT NULL,
    email NVARCHAR(100),
    password NVARCHAR(255) NOT NULL,
    tipo_plan NVARCHAR(10) DEFAULT 'gratuito', -- 'gratuito' o 'premium'
    fecha_suscripcion DATE,
    consultas_diarias INT DEFAULT 0,
    fecha_ultima_consulta DATE,
    estado NVARCHAR(15) DEFAULT 'activo', -- 'activo', 'inactivo', 'suspendido'
    fecha_registro DATETIME DEFAULT GETDATE(),
    fecha_actualizacion DATETIME DEFAULT GETDATE()
);
GO

-- Tabla: tbauditoria_consultas
CREATE TABLE tbauditoria_consultas (
    id INT IDENTITY(1,1) PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_consulta NVARCHAR(50) NOT NULL,
    cantidad_registros INT,
    formato_exportacion NVARCHAR(20),
    fecha_consulta DATETIME DEFAULT GETDATE(),
    ip_usuario NVARCHAR(45),
    FOREIGN KEY (usuario_id) REFERENCES tbusuario(id) ON DELETE CASCADE
);
GO

-- Tabla: tbconfiguraciones
CREATE TABLE tbconfiguraciones (
    id INT IDENTITY(1,1) PRIMARY KEY,
    clave NVARCHAR(100) NOT NULL UNIQUE,
    valor NVARCHAR(MAX) NOT NULL,
    descripcion NVARCHAR(MAX),
    fecha_actualizacion DATETIME DEFAULT GETDATE()
);
GO

-- Tabla: tbplanes
CREATE TABLE tbplanes (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
    precio_mensual DECIMAL(10,2) NOT NULL,
    consultas_diarias INT DEFAULT -1,
    registros_por_tabla INT DEFAULT 10,
    formatos_disponibles NVARCHAR(MAX) NOT NULL,
    datos_personalizados BIT DEFAULT 0,
    descripcion NVARCHAR(MAX),
    activo BIT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT GETDATE()
);
GO

-- Tabla: tbpagos
CREATE TABLE tbpagos (
    id INT IDENTITY(1,1) PRIMARY KEY,
    usuario_id INT NOT NULL,
    plan_id INT NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo_pago NVARCHAR(50) NOT NULL,
    estado_pago NVARCHAR(20) DEFAULT 'pendiente', -- 'pendiente','completado','fallido','cancelado'
    fecha_pago DATETIME DEFAULT GETDATE(),
    fecha_vencimiento DATE NOT NULL,
    referencia_pago NVARCHAR(100),
    FOREIGN KEY (usuario_id) REFERENCES tbusuario(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES tbplanes(id) ON DELETE CASCADE
);
GO

-- Tabla: tbrecuperacion_password
CREATE TABLE tbrecuperacion_password (
    id INT IDENTITY(1,1) PRIMARY KEY,
    usuario_id INT NOT NULL,
    token NVARCHAR(64) NOT NULL,
    fecha_expiracion DATETIME NOT NULL,
    usado BIT DEFAULT 0,
    fecha_creacion DATETIME DEFAULT GETDATE(),
    FOREIGN KEY (usuario_id) REFERENCES tbusuario(id) ON DELETE CASCADE
);
GO

-- Insertar datos de ejemplo
INSERT INTO tbconfiguraciones (clave, valor, descripcion) VALUES
('limite_consultas_gratuito', '3', 'Límite diario para usuarios gratuitos'),
('limite_registros_gratuito', '10', 'Registros por tabla plan gratuito'),
('precio_premium', '9.99', 'Precio mensual plan premium'),
('email_soporte', 'soporte@datafiller.com', 'Email de soporte');
GO

INSERT INTO tbplanes (nombre, precio_mensual, consultas_diarias, registros_por_tabla, formatos_disponibles, datos_personalizados, descripcion, activo) VALUES
('Gratuito', 0.00, 3, 10, 'SQL', 0, 'Plan gratuito', 1),
('Premium', 9.99, -1, 1000, 'SQL,CSV,JSON,XML', 1, 'Plan premium completo', 1);
GO

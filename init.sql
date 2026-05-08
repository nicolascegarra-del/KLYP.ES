CREATE DATABASE IF NOT EXISTS klyp DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klyp;

CREATE TABLE IF NOT EXISTS aplicaciones (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titulo      VARCHAR(100)  NOT NULL,
    descripcion TEXT          NOT NULL,
    icono       VARCHAR(50)   NOT NULL,
    enlace      VARCHAR(255)  DEFAULT '#'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS blog (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    titulo  VARCHAR(200) NOT NULL,
    texto   LONGTEXT     NOT NULL,
    imagen  VARCHAR(255) DEFAULT NULL,
    fecha   DATE         NOT NULL,
    tipo    VARCHAR(50)  DEFAULT 'Noticia'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS usuarios_admin (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nombre   VARCHAR(100) NOT NULL,
    usuario  VARCHAR(50)  NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa   VARCHAR(150) NOT NULL,
    cif              VARCHAR(20)  DEFAULT NULL,
    email            VARCHAR(150) NOT NULL UNIQUE,
    password         VARCHAR(255) DEFAULT NULL,
    direccion        VARCHAR(255) DEFAULT NULL,
    cp               VARCHAR(10)  DEFAULT NULL,
    poblacion        VARCHAR(100) DEFAULT NULL,
    provincia        VARCHAR(100) DEFAULT NULL,
    estado           ENUM('activo','inactivo','pendiente') DEFAULT 'activo',
    fecha_registro   DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS clientes_contactos (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT          NOT NULL,
    nombre     VARCHAR(150) NOT NULL,
    email      VARCHAR(150) DEFAULT NULL,
    telefono   VARCHAR(30)  DEFAULT NULL,
    cargo      VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS suscripciones (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id       INT            NOT NULL,
    ciclo            ENUM('mensual','trimestral','semestral','anual') DEFAULT 'mensual',
    precio           DECIMAL(10,2)  NOT NULL,
    estado           ENUM('activa','pausada','cancelada') DEFAULT 'activa',
    fecha_alta       DATE           DEFAULT (CURRENT_DATE),
    fecha_renovacion DATE           DEFAULT NULL,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS configuracion (
    id                  INT PRIMARY KEY DEFAULT 1,
    email_contacto      VARCHAR(150) DEFAULT '',
    instagram           VARCHAR(255) DEFAULT '',
    linkedin            VARCHAR(255) DEFAULT '',
    facebook            VARCHAR(255) DEFAULT '',
    twitter             VARCHAR(255) DEFAULT '',
    num_noticias_landing INT         DEFAULT 3,
    posicion_noticias   VARCHAR(50)  DEFAULT 'sobre_contacto',
    texto_nosotros      TEXT         DEFAULT NULL,
    texto_hacemos       TEXT         DEFAULT NULL,
    razon_social        VARCHAR(150) DEFAULT '',
    cif                 VARCHAR(20)  DEFAULT '',
    direccion_fiscal    VARCHAR(255) DEFAULT '',
    cp_fiscal           VARCHAR(10)  DEFAULT '',
    poblacion_fiscal    VARCHAR(100) DEFAULT '',
    provincia_fiscal    VARCHAR(100) DEFAULT '',
    telefono_fiscal     VARCHAR(30)  DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS klyp_emails_departamentos (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    departamento  VARCHAR(100) NOT NULL,
    email         VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

-- Datos iniciales
INSERT IGNORE INTO configuracion (id) VALUES (1);

INSERT IGNORE INTO usuarios_admin (nombre, usuario, password) VALUES
('Administrador', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT IGNORE INTO aplicaciones (titulo, descripcion, icono, enlace) VALUES
('Klyp Envío Nóminas',        'Automatiza el envío de nóminas con tan solo un Klyp.',                                     'fa-file-invoice', '#'),
('Klyp Control de Especies',  'Gestiona tu Asociación de Criadores con un portal para socios y control de especies.',      'fa-crow',         '#'),
('Klyp Gastos',               'Gestiona y contabiliza automáticamente los gastos de tus empleados.',                       'fa-euro-sign',    '#');

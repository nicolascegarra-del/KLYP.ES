/* 1. Borramos la base de datos antigua si existe para empezar limpio */
DROP DATABASE IF EXISTS klyp_db;

/* 2. Creamos la base de datos con soporte para acentos y emojis */
CREATE DATABASE klyp_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE klyp_db;

/* 3. Creamos la tabla de APLICACIONES */
CREATE TABLE aplicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    icono VARCHAR(50) NOT NULL,
    enlace VARCHAR(255) DEFAULT '#'
) ENGINE=InnoDB;

/* 4. Insertamos las 3 apps iniciales */
INSERT INTO aplicaciones (titulo, descripcion, icono, enlace) VALUES
('Klyp Envío Nóminas', 'Automatiza el envío de nómina con tan solo un Klyp.', 'fa-file-invoice', '#'),
('Klyp Control de Especies', 'Gestiona tu Asociación de Criadores, con un portal para socios y control de especies.', 'fa-crow', '#'),
('Klyp Gastos', 'Gestiona y contabiliza automáticamente los gastos de tus empleados.', 'fa-euro-sign', '#');

/* 5. Creamos la tabla de ADMINISTRADORES (Con el nuevo campo 'nombre') */
CREATE TABLE usuarios_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, /* Campo nuevo añadido */
    usuario VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

/* 6. Creamos el usuario admin por defecto */
/* Usuario: admin / Contraseña: admin123 */
/* Contraseña por defecto: admin123  (hash bcrypt) */
INSERT INTO usuarios_admin (nombre, usuario, password) VALUES
('Administrador Principal', 'admin', '$2y$10$dQHsSXF2rAebcrzMHXT19ukS8zRlvSb2Rk6H6URewITjQiAFYJNm6');

/* 7. Creamos la tabla de CONFIGURACIÓN WEB */
CREATE TABLE configuracion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email_contacto VARCHAR(255) DEFAULT '',
    instagram VARCHAR(255) DEFAULT '',
    linkedin VARCHAR(255) DEFAULT '',
    facebook VARCHAR(255) DEFAULT '',
    twitter VARCHAR(255) DEFAULT '',
    num_noticias_landing INT DEFAULT 3,
    posicion_noticias VARCHAR(50) DEFAULT 'sobre_contacto',
    texto_nosotros TEXT,
    texto_hacemos TEXT
) ENGINE=InnoDB;

/* 8. Insertamos la configuración inicial */
INSERT INTO configuracion (id, email_contacto, texto_nosotros, texto_hacemos, num_noticias_landing, posicion_noticias) VALUES
(1, 'info@klyp.es', 'Somos una empresa tecnológica especializada en soluciones de gestión empresarial.', 'Desarrollamos aplicaciones a medida para optimizar la gestión de nóminas, contabilidad y procesos internos de tu empresa.', 3, 'sobre_contacto');

/* 9. Creamos la tabla del BLOG */
CREATE TABLE blog (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    texto TEXT NOT NULL,
    imagen VARCHAR(255) DEFAULT '',
    fecha DATE NOT NULL,
    tipo VARCHAR(100) DEFAULT 'Corporativa'
) ENGINE=InnoDB;
-- Schema para hotel_app
CREATE DATABASE IF NOT EXISTS hotel_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_app;

-- Habitaciones
CREATE TABLE IF NOT EXISTS habitaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero VARCHAR(20) NOT NULL UNIQUE,
  tipo ENUM('Sencilla','Doble','Suite') NOT NULL,
  precio_base DECIMAL(10,2) NOT NULL,
  estado_limpieza ENUM('Limpia','Sucia','En Limpieza') NOT NULL DEFAULT 'Limpia',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Huéspedes
CREATE TABLE IF NOT EXISTS huespedes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  documento_identidad VARCHAR(50) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Mantenimiento
CREATE TABLE IF NOT EXISTS mantenimiento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  habitacion_id INT NOT NULL,
  descripcion TEXT,
  fecha_inicio DATE NOT NULL,
  fecha_fin DATE NOT NULL,
  estado ENUM('Abierta','En Progreso','Cerrada') DEFAULT 'Abierta',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE
);

-- Reservas
CREATE TABLE IF NOT EXISTS reservas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  huesped_id INT NOT NULL,
  habitacion_id INT NOT NULL,
  fecha_llegada DATE NOT NULL,
  fecha_salida DATE NOT NULL,
  precio_total DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente','Confirmada','Cancelada') DEFAULT 'Pendiente',
  fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (huesped_id) REFERENCES huespedes(id) ON DELETE CASCADE,
  FOREIGN KEY (habitacion_id) REFERENCES habitaciones(id) ON DELETE CASCADE,
  INDEX idx_habitacion_fechas (habitacion_id, fecha_llegada, fecha_salida)
);

-- Admin por defecto (password: admin123 -> hashed)
CREATE TABLE IF NOT EXISTS admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserciones ejemplo
INSERT IGNORE INTO habitaciones (numero, tipo, precio_base) VALUES
('101','Sencilla', 30.00),
('102','Doble', 50.00),
('201','Suite', 120.00);

INSERT IGNORE INTO admins (username, password_hash) VALUES
('admin', '$2y$10$u1m3n8E7a5kYqGfKcW5yM.6R6q0q7c6EoQ4H1p8bYzQ9tVh3qJp9e'); -- hash de "admin123"

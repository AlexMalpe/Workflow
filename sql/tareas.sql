CREATE TABLE IF NOT EXISTS tareas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    prioridad ENUM('alta', 'media', 'baja') DEFAULT 'media',
    fecha_limite DATE DEFAULT NULL,
    estado ENUM('pendiente', 'en_progreso', 'completada') DEFAULT 'pendiente',
    usuario_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
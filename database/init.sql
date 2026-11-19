CREATE TABLE IF NOT EXISTS citas (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(100) NOT NULL,
    servicio VARCHAR(80) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    UNIQUE KEY horario_unico (fecha, hora)
);
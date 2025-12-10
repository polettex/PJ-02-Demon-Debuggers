-- Agregar columnas para guardar la posición de las mesas
ALTER TABLE recursos 
ADD COLUMN posicion_x DECIMAL(5,2) DEFAULT 50.00,
ADD COLUMN posicion_y DECIMAL(5,2) DEFAULT 50.00;

-- Las posiciones son porcentajes (0-100) relativos al contenedor de la sala
-- Por defecto las mesas aparecen en el centro (50%, 50%)

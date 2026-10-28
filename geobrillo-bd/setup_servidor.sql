CREATE USER IF NOT EXISTS 'geobrillo_app'@'%' IDENTIFIED BY 'cambiar_esta_clave';

GRANT SELECT, INSERT, UPDATE, DELETE ON geobrillo.* TO 'geobrillo_app'@'%';
FLUSH PRIVILEGES;


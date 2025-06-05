-- Cambiar la zona horaria de la base de datos para nuevas conexiones
-- Cambiar la zona horaria solo en la sesión actual
-- Verificar la configuración
ALTER DATABASE "itaxcix-db" SET timezone TO 'America/Lima';
SET TIME ZONE 'America/Lima';
SHOW timezone;

INSERT INTO tb_tipo_documento (tipo_nombre)
VALUES ('DNI'),('PASAPORTE'),('CARNÉ DE EXTRANGERÍA'),
       ('RUC');

INSERT INTO tb_estado_usuario (esta_nombre)
VALUES ('ACTIVO'),('INACTIVO'),('BLOQUEADO');

INSERT INTO tb_tipo_contacto (tipo_nombre)
VALUES ('CORREO ELECTRÓNICO'), ('TELÉFONO MÓVIL');

INSERT INTO tb_tipo_codigo_usuario (tipo_nombre)
VALUES ('VERIFICACIÓN'),('RECUPERACIÓN');

INSERT INTO tb_rol (rol_nombre, rol_activo, rol_web)
VALUES
    ('CIUDADANO', TRUE, FALSE),
    ('CONDUCTOR', TRUE, FALSE),
    ('ADMINISTRADOR', TRUE, TRUE);

INSERT INTO tb_permiso(perm_nombre, perm_activo, perm_web)
VALUES
    ('INICIO CIUDADANO', TRUE,FALSE),
    ('PERFIL CIUDADANO', TRUE,FALSE),
    ('HISTORIAL CIUDADANO', TRUE,FALSE),
    ('INICIO CONDUCTOR', TRUE,FALSE),
    ('PERFIL CONDUCTOR', TRUE,FALSE),
    ('HISTORIAL CONDUCTOR', TRUE,FALSE),
    ('ADMISIÓN DE CONDUCTORES', TRUE,TRUE),
    ('TABLAS MAESTRAS', TRUE,TRUE),
    ('AUDITORIA', TRUE,TRUE),
    ('CONFIGURACIÓN', TRUE,TRUE);

INSERT INTO tb_rol_permiso(rolp_rol_id, rolp_permiso_id, rolp_activo)
VALUES
    (1, 1, TRUE),
    (1, 2, TRUE),
    (1, 3, TRUE),
    (2, 4, TRUE),
    (2, 5, TRUE),
    (2, 6, TRUE),
    (3, 7, TRUE),
    (3, 8, TRUE),
    (3, 9, TRUE),
    (3, 10, TRUE);

INSERT INTO tb_estado_tuc (esta_nombre)
VALUES ('ACTIVO'),('ANULADO'),('VENCIDO');

INSERT INTO tb_estado_conductor (esta_nombre)
VALUES ('APROBADO'),('RECHAZADO'),('PENDIENTE');
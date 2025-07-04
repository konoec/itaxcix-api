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

INSERT INTO tb_estado_viaje (esta_nombre)
VALUES ('SOLICITADO'),('ACEPTADO'),('INICIADO'),
       ('FINALIZADO'),('CANCELADO'),('RECHAZADO');

INSERT INTO tb_departamento(depa_nombre, depa_ubigeo)
VALUES ('LAMBAYEQUE', '140000');

INSERT INTO tb_provincia(prov_nombre, prov_ubigeo, prov_departamento_id)
VALUES ('CHICLAYO', '140100', 1),
       ('FERREÑAFE', '140200', 1),
       ('LAMBAYEQUE', '140300', 1);

INSERT INTO tb_distrito(dist_nombre, dist_ubigeo, dist_provincia_id)
VALUES
    ('CHICLAYO', '140101', 1),
    ('CHONGOYAPE', '140102', 1),
    ('ETEN', '140103', 1),
    ('ETEN PUERTO','140104',1),
    ('JOSÉ LEONARDO ORTIZ', '140105', 1),
    ('LA VICTORIA', '140106', 1),
    ('LAGUNAS', '140107', 1),
    ('MONSEFÚ', '140108', 1),
    ('NUEVA ARICA', '140109', 1),
    ('OYOTÚN', '140110', 1),
    ('PICSI', '140111', 1),
    ('PIMENTEL', '140112', 1),
    ('REQUE', '140113', 1),
    ('SANTA ROSA', '140114', 1),
    ('ZAÑA', '140115', 1),
    ('CAYALTÍ', '140116', 1),
    ('PUCALÁ', '140117', 1),
    ('TÚMAN', '140118', 1),
    ('FERREÑAFE', '140201', 2),
    ('CAÑARÍS', '140202', 2),
    ('INCAHUASI', '140203', 2),
    ('MANUEL ANTONIO MESONES MURO', '140204', 2),
    ('PÍTIPO', '140205', 2),
    ('PUEBLO NUEVO', '140206', 2),
    ('LAMBAYEQUE', '140301', 3),
    ('CHOCHOPE', '140302', 3),
    ('ÍLLIMO', '140303', 3),
    ('JAYANCA', '140304', 3),
    ('MOCHUMI', '140305', 3),
    ('MÓRROPE', '140306', 3),
    ('MOTUPE', '140307', 3),
    ('OLMOS', '140308', 3),
    ('PÁCORA', '140309', 3),
    ('SALAS', '140310', 3),
    ('SAN JOSÉ', '140311', 3),
    ('TÚCUME', '140312', 3);

INSERT INTO tb_configuracion (conf_clave, conf_valor, conf_activo)
VALUES ('ITAXCIX_NUMERO_EMERGENCIA', '+51936713121', TRUE);

INSERT INTO tb_tipo_incidencia(tipo_nombre, tipo_activo)
VALUES ('FALTA DE RESPETO', TRUE),
       ('FALTA DE PUNTUALIDAD', TRUE),
       ('FALTA DE HIGIENE', TRUE),
       ('FALTA DE SEGURIDAD', TRUE),
       ('FALTA DE CUMPLIMIENTO', TRUE),
       ('OTRO', TRUE);

INSERT INTO public.tb_centro_ayuda (ayuda_titulo, ayuda_subtitulo, ayuda_respuesta, ayuda_activo)
VALUES
    ('¿Cómo solicito un taxi?', 'Guía rápida para pedir un taxi desde la app', 'Abre la aplicación, permite el acceso a tu ubicación, selecciona tu destino y confirma tu pedido.', true),
    ('¿Qué hago si el conductor no llega?', 'Pasos en caso de demora', 'Puedes cancelar el viaje sin penalidad si el conductor demora más de 10 minutos. También puedes contactar al conductor desde la app.', true),
    ('¿Puedo calificar al conductor?', 'Sistema de calificación', 'Sí, al finalizar el viaje podrás calificar al conductor con estrellas y dejar un comentario.', true);
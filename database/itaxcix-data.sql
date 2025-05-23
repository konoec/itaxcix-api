INSERT INTO tb_tipo_documento (tipo_nombre)
VALUES ('DNI'),('Pasaporte'),('Carné de Extranjería'),
       ('RUC');

INSERT INTO tb_estado_usuario (esta_nombre)
VALUES ('Activo'),('Inactivo'),('Bloqueado');

INSERT INTO tb_tipo_contacto (tipo_nombre)
VALUES ('Correo Electrónico'), ('Teléfono Móvil');

INSERT INTO tb_tipo_codigo_usuario (tipo_nombre)
VALUES ('Verificación'),('Recuperación');

INSERT INTO tb_rol (rol_nombre, rol_activo)
VALUES
    ('Ciudadano', TRUE),
    ('Conductor', TRUE),
    ('Administrador', TRUE);

INSERT INTO tb_departamento (depa_id, depa_nombre, depa_ubigeo)
VALUES (1, 'Lambayeque', '140000');

INSERT INTO tb_provincia (prov_id, prov_nombre, prov_departamento_id, prov_ubigeo)
VALUES (1, 'Chiclayo', 1, '140100');

INSERT INTO tb_distrito (dist_id, dist_nombre, dist_provincia_id, dist_ubigeo)
VALUES (1, 'Chiclayo', 1, '140101');

INSERT INTO tb_estado_tuc (esta_nombre)
VALUES ('Activo'),('Anulado'),('Vencido');
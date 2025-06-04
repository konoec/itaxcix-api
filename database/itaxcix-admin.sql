-- 1. Insertar persona
INSERT INTO tb_persona (
    pers_nombre,
    pers_apellido,
    pers_documento,
    pers_tipo_documento_id,
    pers_activo
) VALUES (
             'Administrador',
             'Principal',
             '00000000', -- DNI ficticio
             1,          -- tipo_documento_id = DNI
             TRUE
         ) RETURNING pers_id;

-- 2. Insertar usuario
INSERT INTO tb_usuario (
    usua_clave,
    usua_persona_id,
    usua_estado_id
) VALUES (
             '$2y$10$5H91LNdWlaK0m21FSDJFquQrs4qGLqqKrgwhgGnZQ/GdNo0plJRmy', -- password_hash('Password@123', PASSWORD_DEFAULT)
             1, -- pers_id obtenido del paso anterior
             1  -- estado ACTIVO
         ) RETURNING usua_id;

-- 3. Insertar contacto del usuario (correo electrónico)
INSERT INTO tb_contacto_usuario (
    cont_valor,
    cont_confirmado,
    cont_activo,
    cont_usuario_id,
    cont_tipo_id
) VALUES (
             'itaxcix@gmail.com', -- Correo del administrador
             TRUE,                -- Confirmado
             TRUE,                -- Activo
             1,                   -- usua_id del paso anterior
             1                    -- tipo_id = CORREO ELECTRÓNICO
         );

-- 4. Asignar rol de administrador
INSERT INTO tb_rol_usuario (
    rolu_rol_id,
    rolu_usuario_id,
    rolu_activo
) VALUES (
             3, -- rol_id = ADMINISTRADOR
             1, -- usua_id
             TRUE
         );

-- 5. Crear perfil de administrador
INSERT INTO tb_perfil_administrador (
    perf_area,
    perf_cargo,
    perf_usuario_id
) VALUES (
             'Sistemas',
             'Administrador General',
             1 -- usua_id
         );
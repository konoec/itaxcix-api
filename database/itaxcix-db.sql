CREATE TABLE "tb_tipo_documento" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(50) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_persona" (
  "pers_id" serial PRIMARY KEY,
  "pers_nombre" varchar(100),
  "pers_apellido" varchar(100),
  "pers_tipo_documento_id" int,
  "pers_documento" varchar(20),
  "pers_imagen" varchar(255),
  "pers_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_estado_usuario" (
  "esta_id" serial PRIMARY KEY,
  "esta_nombre" varchar(50) NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_usuario" (
  "usua_id" serial PRIMARY KEY,
  "usua_alias" varchar(50),
  "usua_clave" varchar(255),
  "usua_persona_id" int,
  "usua_estado_id" int
);

CREATE TABLE "tb_tipo_contacto" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(50) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_contacto_usuario" (
  "cont_id" serial PRIMARY KEY,
  "cont_usuario_id" int,
  "cont_tipo_id" int,
  "cont_valor" varchar(100) NOT NULL,
  "cont_confirmado" boolean NOT NULL,
  "cont_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_codigo_usuario" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(50) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_codigo_usuario" (
  "codi_id" serial PRIMARY KEY,
  "codi_usuario_id" int,
  "codi_tipo_id" int,
  "codi_contacto_id" int,
  "codi_codigo" varchar(8) NOT NULL,
  "codi_fecha_expiracion" timestamp NOT NULL,
  "codi_fecha_uso" timestamp,
  "codi_usado" boolean NOT NULL DEFAULT false
);

CREATE TABLE "tb_coordenadas" (
  "coor_id" serial PRIMARY KEY,
  "coor_nombre" varchar(100) NOT NULL,
  "coor_distrito_id" int,
  "coor_latitud" varchar(20) NOT NULL,
  "coor_longitud" varchar(20) NOT NULL
);

CREATE TABLE "tb_estado_viaje" (
  "esta_id" serial PRIMARY KEY,
  "esta_nombre" varchar(50) NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_viaje" (
  "viaj_id" serial PRIMARY KEY,
  "viaj_usuario_ciudadano_id" int,
  "viaj_usuario_conductor_id" int,
  "viaj_origen_id" int,
  "viaj_destino_id" int,
  "viaj_fecha_inicio" timestamp,
  "viaj_fecha_fin" timestamp,
  "viaj_fecha_creacion" timestamp,
  "viaj_estado_id" int
);

CREATE TABLE "tb_calificacion" (
  "cali_id" serial PRIMARY KEY,
  "cali_calificador_id" int,
  "cali_calificado_id" int,
  "cali_viaje_id" int,
  "cali_puntaje" int NOT NULL,
  "cali_comentario" varchar(255)
);

CREATE TABLE "tb_tipo_incidencia" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(100) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_incidencia" (
  "inci_id" serial PRIMARY KEY,
  "inci_usuario_id" int,
  "inci_viaje_id" int,
  "inci_tipo_id" int,
  "inci_comentario" varchar(255),
  "inci_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_departamento" (
  "depa_id" serial PRIMARY KEY,
  "depa_nombre" varchar(50),
  "depa_ubigeo" varchar(6)
);

CREATE TABLE "tb_provincia" (
  "prov_id" serial PRIMARY KEY,
  "prov_nombre" varchar(50),
  "prov_departamento_id" int,
  "prov_ubigeo" varchar(6)
);

CREATE TABLE "tb_distrito" (
  "dist_id" serial PRIMARY KEY,
  "dist_nombre" varchar(50),
  "dist_provincia_id" int,
  "dist_ubigeo" varchar(6)
);

CREATE TABLE "tb_tipo_combustible" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(50) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_marca" (
  "marc_id" serial PRIMARY KEY,
  "marc_nombre" varchar(50) NOT NULL,
  "marc_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_clase_vehiculo" (
  "clas_id" serial PRIMARY KEY,
  "clas_nombre" varchar(50) NOT NULL,
  "clas_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_categoria_vehiculo" (
  "cate_id" serial PRIMARY KEY,
  "cate_nombre" varchar(50) NOT NULL,
  "cate_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_vehiculo" (
  "vehi_id" serial PRIMARY KEY,
  "vehi_placa" varchar(10) UNIQUE NOT NULL,
  "vehi_modelo_id" int,
  "vehi_color_id" int,
  "vehi_anio_fabric" int,
  "vehi_num_asientos" int,
  "vehi_num_pasajeros" int,
  "vehi_tipo_combustible_id" int,
  "vehi_clase_id" int,
  "vehi_categoria_id" int,
  "vehi_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_color" (
  "colo_id" serial PRIMARY KEY,
  "colo_nombre" varchar(50) NOT NULL,
  "colo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_modelo" (
  "mode_id" serial PRIMARY KEY,
  "mode_nombre" varchar(50) NOT NULL,
  "mode_marca_id" int,
  "mode_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_especificacion_tecnica" (
  "espe_id" serial PRIMARY KEY,
  "espe_vehiculo_id" int,
  "espe_peso_seco" numeric,
  "espe_peso_bruto" numeric,
  "espe_longitud" numeric,
  "espe_altura" numeric,
  "espe_anchura" numeric,
  "espe_carga_util" numeric
);

CREATE TABLE "tb_estado_tuc" (
  "esta_id" serial PRIMARY KEY,
  "esta_nombre" varchar(50) NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_empresa" (
  "empr_id" serial PRIMARY KEY,
  "empr_ruc" varchar(11),
  "empr_nombre" varchar(100),
  "empr_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_tramite" (
  "tipo_id" serial PRIMARY KEY,
  "tipo_nombre" varchar(50),
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tramite_tuc" (
  "tram_id" serial PRIMARY KEY,
  "tram_codigo" varchar(8),
  "tram_usuario_id" int,
  "tram_vehiculo_id" int,
  "tram_empresa_id" int,
  "tram_distrito_id" int,
  "tram_estado_id" int,
  "tram_tipo_id" int,
  "tram_modalidad_id" int,
  "tram_fecha_tramite" date,
  "tram_fecha_emision" date,
  "tram_fecha_caducidad" date
);

CREATE TABLE "tb_modalidad_tuc" (
  "moda_id" serial PRIMARY KEY,
  "moda_nombre" varchar(50) UNIQUE,
  "moda_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_configuracion" (
  "conf_id" serial PRIMARY KEY,
  "conf_clave" varchar(50),
  "conf_valor" varchar(255),
  "conf_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_auditoria" (
  "audi_id" serial PRIMARY KEY,
  "audi_tabla_afectada" text,
  "audi_operacion" text,
  "audi_usuario_sistema" varchar(100),
  "audi_fecha" timestamp DEFAULT CURRENT_TIMESTAMP,
  "audi_dato_anterior" jsonb,
  "audi_dato_nuevo" jsonb
);

CREATE TABLE "tb_ruta_servicio" (
  "ruta_id" serial PRIMARY KEY,
  "ruta_tram_id" int,
  "ruta_tipo_servicio" varchar(50),
  "ruta_texto" text,
  "ruta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_gravedad_infraccion" (
  "grav_id" serial PRIMARY KEY,
  "grav_nombre" varchar(100) UNIQUE,
  "grav_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_estado_infraccion" (
  "esta_id" serial PRIMARY KEY,
  "esta_nombre" varchar(100) UNIQUE,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_infraccion" (
  "infr_id" serial PRIMARY KEY,
  "infr_usuario_id" int,
  "infr_gravedad_id" int,
  "infr_fecha" timestamp DEFAULT CURRENT_TIMESTAMP,
  "infr_descripcion" text,
  "infr_estado_id" int
);

CREATE TABLE "tb_rol" (
  "rol_id" serial PRIMARY KEY,
  "rol_nombre" varchar(50) UNIQUE NOT NULL,
  "rol_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_permiso" (
  "perm_id" serial PRIMARY KEY,
  "perm_nombre" varchar(100) UNIQUE NOT NULL,
  "perm_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_rol_usuario" (
  "rolu_id" serial PRIMARY KEY,
  "rolu_usuario_id" int,
  "rolu_rol_id" int,
  "rolu_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_rol_permiso" (
  "rolp_id" serial PRIMARY KEY,
  "rolp_rol_id" int,
  "rolp_permiso_id" int,
  "rolp_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_perfil_administrador" (
  "perf_id" serial PRIMARY KEY,
  "perf_usuario_id" int UNIQUE,
  "perf_area" varchar(100),
  "perf_cargo" varchar(100)
);

CREATE TABLE "tb_perfil_conductor" (
  "perf_id" serial PRIMARY KEY,
  "perf_usuario_id" int UNIQUE,
  "perf_disponible" boolean DEFAULT false
);

ALTER TABLE "tb_persona" ADD FOREIGN KEY ("pers_tipo_documento_id") REFERENCES "tb_tipo_documento" ("tipo_id");

ALTER TABLE "tb_usuario" ADD FOREIGN KEY ("usua_persona_id") REFERENCES "tb_persona" ("pers_id");

ALTER TABLE "tb_usuario" ADD FOREIGN KEY ("usua_estado_id") REFERENCES "tb_estado_usuario" ("esta_id");

ALTER TABLE "tb_contacto_usuario" ADD FOREIGN KEY ("cont_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_contacto_usuario" ADD FOREIGN KEY ("cont_tipo_id") REFERENCES "tb_tipo_contacto" ("tipo_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_tipo_id") REFERENCES "tb_tipo_codigo_usuario" ("tipo_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_contacto_id") REFERENCES "tb_contacto_usuario" ("cont_id");

ALTER TABLE "tb_coordenadas" ADD FOREIGN KEY ("coor_distrito_id") REFERENCES "tb_distrito" ("dist_id");

ALTER TABLE "tb_viaje" ADD FOREIGN KEY ("viaj_usuario_ciudadano_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_viaje" ADD FOREIGN KEY ("viaj_usuario_conductor_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_viaje" ADD FOREIGN KEY ("viaj_origen_id") REFERENCES "tb_coordenadas" ("coor_id");

ALTER TABLE "tb_viaje" ADD FOREIGN KEY ("viaj_destino_id") REFERENCES "tb_coordenadas" ("coor_id");

ALTER TABLE "tb_viaje" ADD FOREIGN KEY ("viaj_estado_id") REFERENCES "tb_estado_viaje" ("esta_id");

ALTER TABLE "tb_calificacion" ADD FOREIGN KEY ("cali_calificador_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_calificacion" ADD FOREIGN KEY ("cali_calificado_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_calificacion" ADD FOREIGN KEY ("cali_viaje_id") REFERENCES "tb_viaje" ("viaj_id");

ALTER TABLE "tb_incidencia" ADD FOREIGN KEY ("inci_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_incidencia" ADD FOREIGN KEY ("inci_viaje_id") REFERENCES "tb_viaje" ("viaj_id");

ALTER TABLE "tb_incidencia" ADD FOREIGN KEY ("inci_tipo_id") REFERENCES "tb_tipo_incidencia" ("tipo_id");

ALTER TABLE "tb_provincia" ADD FOREIGN KEY ("prov_departamento_id") REFERENCES "tb_departamento" ("depa_id");

ALTER TABLE "tb_distrito" ADD FOREIGN KEY ("dist_provincia_id") REFERENCES "tb_provincia" ("prov_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_modelo_id") REFERENCES "tb_modelo" ("mode_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_color_id") REFERENCES "tb_color" ("colo_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_tipo_combustible_id") REFERENCES "tb_tipo_combustible" ("tipo_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_clase_id") REFERENCES "tb_clase_vehiculo" ("clas_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_categoria_id") REFERENCES "tb_categoria_vehiculo" ("cate_id");

ALTER TABLE "tb_modelo" ADD FOREIGN KEY ("mode_marca_id") REFERENCES "tb_marca" ("marc_id");

ALTER TABLE "tb_especificacion_tecnica" ADD FOREIGN KEY ("espe_vehiculo_id") REFERENCES "tb_vehiculo" ("vehi_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_vehiculo_id") REFERENCES "tb_vehiculo" ("vehi_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_empresa_id") REFERENCES "tb_empresa" ("empr_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_distrito_id") REFERENCES "tb_distrito" ("dist_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_estado_id") REFERENCES "tb_estado_tuc" ("esta_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_tipo_id") REFERENCES "tb_tipo_tramite" ("tipo_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_modalidad_id") REFERENCES "tb_modalidad_tuc" ("moda_id");

ALTER TABLE "tb_ruta_servicio" ADD FOREIGN KEY ("ruta_tram_id") REFERENCES "tb_tramite_tuc" ("tram_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_gravedad_id") REFERENCES "tb_gravedad_infraccion" ("grav_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_estado_id") REFERENCES "tb_estado_infraccion" ("esta_id");

ALTER TABLE "tb_rol_usuario" ADD FOREIGN KEY ("rolu_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_rol_usuario" ADD FOREIGN KEY ("rolu_rol_id") REFERENCES "tb_rol" ("rol_id");

ALTER TABLE "tb_rol_permiso" ADD FOREIGN KEY ("rolp_rol_id") REFERENCES "tb_rol" ("rol_id");

ALTER TABLE "tb_rol_permiso" ADD FOREIGN KEY ("rolp_permiso_id") REFERENCES "tb_permiso" ("perm_id");

ALTER TABLE "tb_perfil_administrador" ADD FOREIGN KEY ("perf_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_perfil_conductor" ADD FOREIGN KEY ("perf_usuario_id") REFERENCES "tb_usuario" ("usua_id");

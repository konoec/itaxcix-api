CREATE TABLE "tb_auditoria" (
  "audi_id" integer PRIMARY KEY,
  "audi_tabla_afectada" text NOT NULL,
  "audi_operacion" text NOT NULL,
  "audi_usuario_sistema" varchar(100) NOT NULL,
  "audi_fecha" timestamp NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  "audi_dato_anterior" json,
  "audi_dato_nuevo" json
);

CREATE TABLE "tb_configuracion" (
  "conf_id" integer PRIMARY KEY,
  "conf_clave" varchar(50) NOT NULL,
  "conf_valor" varchar(255) NOT NULL,
  "conf_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_incidencia" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(100) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_estado_infraccion" (
  "esta_id" integer PRIMARY KEY,
  "esta_nombre" varchar(100) UNIQUE NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_gravedad_infraccion" (
  "grav_id" integer PRIMARY KEY,
  "grav_nombre" varchar(100) UNIQUE NOT NULL,
  "grav_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_documento" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) UNIQUE NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_persona" (
  "pers_id" integer PRIMARY KEY,
  "pers_nombre" varchar(100) NOT NULL,
  "pers_apellido" varchar(100) NOT NULL,
  "pers_documento" varchar(20) UNIQUE NOT NULL,
  "pers_imagen" varchar(255),
  "pers_activo" boolean NOT NULL DEFAULT true,
  "pers_tipo_documento_id" integer
);

CREATE TABLE "tb_empresa" (
  "empr_id" integer PRIMARY KEY,
  "empr_ruc" varchar(11) UNIQUE,
  "empr_nombre" varchar(100) NOT NULL,
  "empr_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_estado_tuc" (
  "esta_id" integer PRIMARY KEY,
  "esta_nombre" varchar(50) UNIQUE NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_modalidad_tuc" (
  "moda_id" integer PRIMARY KEY,
  "moda_nombre" varchar(50) UNIQUE NOT NULL,
  "moda_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_servicio" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) UNIQUE NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_tramite" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) UNIQUE NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_departamento" (
  "depa_id" integer PRIMARY KEY,
  "depa_nombre" varchar(50) UNIQUE,
  "depa_ubigeo" varchar(6) UNIQUE
);

CREATE TABLE "tb_provincia" (
  "prov_id" integer PRIMARY KEY,
  "prov_nombre" varchar(50) UNIQUE,
  "prov_ubigeo" varchar(6) UNIQUE,
  "prov_departamento_id" integer
);

CREATE TABLE "tb_distrito" (
  "dist_id" integer PRIMARY KEY,
  "dist_nombre" varchar(50) UNIQUE,
  "dist_ubigeo" varchar(6) UNIQUE,
  "dist_provincia_id" integer
);

CREATE TABLE "tb_coordenadas" (
  "coor_id" integer PRIMARY KEY,
  "coor_nombre" varchar(100) NOT NULL,
  "coor_latitud" varchar(20) NOT NULL,
  "coor_longitud" varchar(20) NOT NULL,
  "coor_distrito_id" integer
);

CREATE TABLE "tb_estado_usuario" (
  "esta_id" integer PRIMARY KEY,
  "esta_nombre" varchar(50) UNIQUE NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_permiso" (
  "perm_id" integer PRIMARY KEY,
  "perm_nombre" varchar(100) UNIQUE NOT NULL,
  "perm_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_rol" (
  "rol_id" integer PRIMARY KEY,
  "rol_nombre" varchar(50) UNIQUE NOT NULL,
  "rol_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_rol_permiso" (
  "rolp_id" integer PRIMARY KEY,
  "rolp_activo" boolean NOT NULL DEFAULT true,
  "rolp_rol_id" integer,
  "rolp_permiso_id" integer
);

CREATE TABLE "tb_tipo_codigo_usuario" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_tipo_contacto" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) UNIQUE NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_usuario" (
  "usua_id" integer PRIMARY KEY,
  "usua_alias" varchar(50) UNIQUE NOT NULL,
  "usua_clave" varchar(255) NOT NULL,
  "usua_persona_id" integer UNIQUE,
  "usua_estado_id" integer
);

CREATE TABLE "tb_infraccion" (
  "infr_id" integer PRIMARY KEY,
  "infr_fecha" timestamp NOT NULL DEFAULT (CURRENT_TIMESTAMP),
  "infr_descripcion" text,
  "infr_usuario_id" integer,
  "infr_gravedad_id" integer,
  "infr_estado_id" integer
);

CREATE TABLE "tb_contacto_usuario" (
  "cont_id" integer PRIMARY KEY,
  "cont_valor" varchar(100) UNIQUE NOT NULL,
  "cont_confirmado" boolean NOT NULL,
  "cont_activo" boolean NOT NULL DEFAULT true,
  "cont_usuario_id" integer,
  "cont_tipo_id" integer
);

CREATE TABLE "tb_codigo_usuario" (
  "codi_id" integer PRIMARY KEY,
  "codi_codigo" varchar(8) NOT NULL,
  "codi_fecha_expiracion" timestamp NOT NULL,
  "codi_fecha_uso" timestamp,
  "codi_usado" boolean NOT NULL DEFAULT false,
  "codi_usuario_id" integer,
  "codi_tipo_id" integer,
  "codi_contacto_id" integer
);

CREATE TABLE "tb_perfil_administrador" (
  "perf_id" integer PRIMARY KEY,
  "perf_area" varchar(100) NOT NULL,
  "perf_cargo" varchar(100) NOT NULL,
  "perf_usuario_id" integer
);

CREATE TABLE "tb_perfil_conductor" (
  "perf_id" integer PRIMARY KEY,
  "perf_disponible" boolean NOT NULL DEFAULT false,
  "perf_usuario_id" integer UNIQUE
);

CREATE TABLE "tb_rol_usuario" (
  "rolu_id" integer PRIMARY KEY,
  "rolu_activo" boolean NOT NULL DEFAULT true,
  "rolu_rol_id" integer,
  "rolu_usuario_id" integer
);

CREATE TABLE "tb_categoria_vehiculo" (
  "cate_id" integer PRIMARY KEY,
  "cate_nombre" varchar(50) UNIQUE,
  "cate_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_clase_vehiculo" (
  "clas_id" integer PRIMARY KEY,
  "clas_nombre" varchar(50) UNIQUE NOT NULL,
  "clas_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_color" (
  "colo_id" integer PRIMARY KEY,
  "colo_nombre" varchar(50) UNIQUE NOT NULL,
  "colo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_marca" (
  "marc_id" integer PRIMARY KEY,
  "marc_nombre" varchar(50) UNIQUE NOT NULL,
  "marc_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_modelo" (
  "mode_id" integer PRIMARY KEY,
  "mode_nombre" varchar(50) NOT NULL,
  "mode_activo" boolean NOT NULL DEFAULT true,
  "mode_marca_id" integer
);

CREATE TABLE "tb_tipo_combustible" (
  "tipo_id" integer PRIMARY KEY,
  "tipo_nombre" varchar(50) UNIQUE NOT NULL,
  "tipo_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_vehiculo" (
  "vehi_id" integer PRIMARY KEY,
  "vehi_placa" varchar(10) UNIQUE NOT NULL,
  "vehi_anio_fabric" integer,
  "vehi_num_asientos" integer,
  "vehi_num_pasajeros" integer,
  "vehi_activo" boolean NOT NULL DEFAULT true,
  "vehi_modelo_id" integer,
  "vehi_color_id" integer,
  "vehi_tipo_combustible_id" integer,
  "vehi_clase_id" integer,
  "vehi_categoria_id" integer
);

CREATE TABLE "tb_tramite_tuc" (
  "tram_id" integer PRIMARY KEY,
  "tram_codigo" varchar(8) UNIQUE NOT NULL,
  "tram_fecha_tramite" date,
  "tram_fecha_emision" date,
  "tram_fecha_caducidad" date,
  "tram_vehiculo_id" integer,
  "tram_empresa_id" integer,
  "tram_distrito_id" integer,
  "tram_estado_id" integer,
  "tram_tipo_id" integer,
  "tram_modalidad_id" integer
);

CREATE TABLE "tb_ruta_servicio" (
  "ruta_id" integer PRIMARY KEY,
  "ruta_texto" text,
  "ruta_activo" boolean NOT NULL DEFAULT true,
  "ruta_tram_id" integer,
  "ruta_tipo_id" integer
);

CREATE TABLE "tb_vehiculo_usuario" (
  "cont_id" integer PRIMARY KEY,
  "cont_activo" boolean NOT NULL DEFAULT true,
  "cont_usuario_id" integer,
  "vehi_vehiculo_id" integer
);

CREATE TABLE "tb_especificacion_tecnica" (
  "espe_id" integer PRIMARY KEY,
  "espe_peso_seco" numeric(10,2),
  "espe_peso_bruto" numeric(10,2),
  "espe_longitud" numeric(10,2),
  "espe_altura" numeric(10,2),
  "espe_anchura" numeric(10,2),
  "espe_carga_util" numeric(10,2),
  "espe_vehiculo_id" integer UNIQUE
);

CREATE TABLE "tb_estado_viaje" (
  "esta_id" integer PRIMARY KEY,
  "esta_nombre" varchar(50) NOT NULL,
  "esta_activo" boolean NOT NULL DEFAULT true
);

CREATE TABLE "tb_viaje" (
  "viaj_id" integer PRIMARY KEY,
  "viaj_fecha_inicio" timestamp,
  "viaj_fecha_fin" timestamp,
  "viaj_fecha_creacion" timestamp NOT NULL,
  "viaj_usuario_ciudadano_id" integer,
  "viaj_usuario_conductor_id" integer,
  "viaj_origen_id" integer,
  "viaj_destino_id" integer,
  "viaj_estado_id" integer
);

CREATE TABLE "tb_calificacion" (
  "cali_id" integer PRIMARY KEY,
  "cali_puntaje" integer NOT NULL,
  "cali_comentario" varchar(255),
  "cali_calificador_id" integer,
  "cali_calificado_id" integer,
  "cali_viaje_id" integer
);

CREATE TABLE "tb_incidencia" (
  "inci_id" integer PRIMARY KEY,
  "inci_comentario" varchar(255),
  "inci_activo" boolean NOT NULL DEFAULT true,
  "inci_usuario_id" integer,
  "inci_viaje_id" integer,
  "inci_tipo_id" integer
);

ALTER TABLE "tb_persona" ADD FOREIGN KEY ("pers_tipo_documento_id") REFERENCES "tb_tipo_documento" ("tipo_id");

ALTER TABLE "tb_provincia" ADD FOREIGN KEY ("prov_departamento_id") REFERENCES "tb_departamento" ("depa_id");

ALTER TABLE "tb_distrito" ADD FOREIGN KEY ("dist_provincia_id") REFERENCES "tb_provincia" ("prov_id");

ALTER TABLE "tb_coordenadas" ADD FOREIGN KEY ("coor_distrito_id") REFERENCES "tb_distrito" ("dist_id");

ALTER TABLE "tb_rol_permiso" ADD FOREIGN KEY ("rolp_rol_id") REFERENCES "tb_rol" ("rol_id");

ALTER TABLE "tb_rol_permiso" ADD FOREIGN KEY ("rolp_permiso_id") REFERENCES "tb_permiso" ("perm_id");

ALTER TABLE "tb_usuario" ADD FOREIGN KEY ("usua_persona_id") REFERENCES "tb_persona" ("pers_id");

ALTER TABLE "tb_usuario" ADD FOREIGN KEY ("usua_estado_id") REFERENCES "tb_estado_usuario" ("esta_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_gravedad_id") REFERENCES "tb_gravedad_infraccion" ("grav_id");

ALTER TABLE "tb_infraccion" ADD FOREIGN KEY ("infr_estado_id") REFERENCES "tb_estado_infraccion" ("esta_id");

ALTER TABLE "tb_contacto_usuario" ADD FOREIGN KEY ("cont_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_contacto_usuario" ADD FOREIGN KEY ("cont_tipo_id") REFERENCES "tb_tipo_contacto" ("tipo_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_tipo_id") REFERENCES "tb_tipo_codigo_usuario" ("tipo_id");

ALTER TABLE "tb_codigo_usuario" ADD FOREIGN KEY ("codi_contacto_id") REFERENCES "tb_contacto_usuario" ("cont_id");

ALTER TABLE "tb_perfil_administrador" ADD FOREIGN KEY ("perf_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_perfil_conductor" ADD FOREIGN KEY ("perf_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_rol_usuario" ADD FOREIGN KEY ("rolu_rol_id") REFERENCES "tb_rol" ("rol_id");

ALTER TABLE "tb_rol_usuario" ADD FOREIGN KEY ("rolu_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_modelo" ADD FOREIGN KEY ("mode_marca_id") REFERENCES "tb_marca" ("marc_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_modelo_id") REFERENCES "tb_modelo" ("mode_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_color_id") REFERENCES "tb_color" ("colo_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_tipo_combustible_id") REFERENCES "tb_tipo_combustible" ("tipo_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_clase_id") REFERENCES "tb_clase_vehiculo" ("clas_id");

ALTER TABLE "tb_vehiculo" ADD FOREIGN KEY ("vehi_categoria_id") REFERENCES "tb_categoria_vehiculo" ("cate_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_vehiculo_id") REFERENCES "tb_vehiculo" ("vehi_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_empresa_id") REFERENCES "tb_empresa" ("empr_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_distrito_id") REFERENCES "tb_distrito" ("dist_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_estado_id") REFERENCES "tb_estado_tuc" ("esta_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_tipo_id") REFERENCES "tb_tipo_tramite" ("tipo_id");

ALTER TABLE "tb_tramite_tuc" ADD FOREIGN KEY ("tram_modalidad_id") REFERENCES "tb_modalidad_tuc" ("moda_id");

ALTER TABLE "tb_ruta_servicio" ADD FOREIGN KEY ("ruta_tram_id") REFERENCES "tb_tramite_tuc" ("tram_id");

ALTER TABLE "tb_ruta_servicio" ADD FOREIGN KEY ("ruta_tipo_id") REFERENCES "tb_tipo_servicio" ("tipo_id");

ALTER TABLE "tb_vehiculo_usuario" ADD FOREIGN KEY ("cont_usuario_id") REFERENCES "tb_usuario" ("usua_id");

ALTER TABLE "tb_vehiculo_usuario" ADD FOREIGN KEY ("vehi_vehiculo_id") REFERENCES "tb_vehiculo" ("vehi_id");

ALTER TABLE "tb_especificacion_tecnica" ADD FOREIGN KEY ("espe_vehiculo_id") REFERENCES "tb_vehiculo" ("vehi_id");

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

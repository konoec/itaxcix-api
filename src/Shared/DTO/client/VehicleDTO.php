<?php

namespace itaxcix\Shared\DTO\client;

readonly class VehicleDTO
{
    public function __construct(
        public ?string $departamento = null,
        public ?string $provincia = null,
        public ?string $distrito = null,
        public ?string $ubigeo = null,
        public ?string $gobLocal = null,
        public ?string $ruc = null,
        public ?string $placa = null,
        public ?string $numAsientos = null,
        public ?string $anioFabric = null,
        public ?string $tipoCombust = null,
        public ?string $pesoSeco = null,
        public ?string $pesoBruto = null,
        public ?string $longitud = null,
        public ?string $altura = null,
        public ?string $anchura = null,
        public ?string $cargaUtil = null,
        public ?string $numPasaj = null,
        public ?string $marca = null,
        public ?string $modelo = null,
        public ?string $color = null,
        public ?string $clase = null,
        public ?string $tramite = null,
        public ?string $fechaTram = null,
        public ?string $modalidad = null,
        public ?string $fechaEmi = null,
        public ?string $fechaCaduc = null,
        public ?string $rucEmpresa = null,
        public ?string $tipServ = null,
        public ?string $ruta = null,
        public ?string $categoria = null,
        public ?string $propietario = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            departamento: $data['DEPARTAMENTO'] ?? null,
            provincia: $data['PROVINCIA'] ?? null,
            distrito: $data['DISTRITO'] ?? null,
            ubigeo: $data['UBIGEO'] ?? null,
            gobLocal: $data['GOB_LOCAL'] ?? null,
            ruc: $data['RUC'] ?? null,
            placa: $data['PLACA'] ?? null,
            numAsientos: $data['NUM_ASIENTOS'] ?? null,
            anioFabric: $data['ANIO_FABRIC'] ?? null,
            tipoCombust: $data['TIPO_COMBUST'] ?? null,
            pesoSeco: $data['PESO_SECO'] ?? null,
            pesoBruto: $data['PESO_BRUTO'] ?? null,
            longitud: $data['LONGITUD'] ?? null,
            altura: $data['ALTURA'] ?? null,
            anchura: $data['ANCHURA'] ?? null,
            cargaUtil: $data['CARGA_UTIL'] ?? null,
            numPasaj: $data['NUM_PASAJ'] ?? null,
            marca: $data['MARCA'] ?? null,
            modelo: $data['MODELO'] ?? null,
            color: $data['COLOR'] ?? null,
            clase: $data['CLASE'] ?? null,
            tramite: $data['TRAMITE'] ?? null,
            fechaTram: $data['FECHA_TRAM'] ?? null,
            modalidad: $data['MODALIDAD'] ?? null,
            fechaEmi: $data['FECHA_EMI'] ?? null,
            fechaCaduc: $data['FECHA_CADUC'] ?? null,
            rucEmpresa: $data['RUC_EMPRESA'] ?? null,
            tipServ: $data['TIP_SERV'] ?? null,
            ruta: $data['RUTA'] ?? null,
            categoria: $data['CATEGORIA'] ?? null,
            propietario: $data['PROPIETARIO'] ?? null
        );
    }
}
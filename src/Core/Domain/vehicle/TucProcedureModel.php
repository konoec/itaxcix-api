<?php

namespace itaxcix\Core\Domain\vehicle;

use DateTime;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Domain\location\DistrictModel;

class TucProcedureModel {
    private int $id;
    private ?string $code = null;
    private ?VehicleModel $vehicle = null;
    private ?CompanyModel $company = null;
    private ?DistrictModel $district = null;
    private ?TucStatusModel $status = null;
    private ?ProcedureTypeModel $type = null;
    private ?TucModalityModel $modality = null;
    private ?DateTime $procedureDate = null;
    private ?DateTime $issueDate = null;
    private ?DateTime $expirationDate = null;
}
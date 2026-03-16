<?php

namespace App\Transformer;

use App\DTO\Company\CompanyOutputDTO;
use App\Entity\Company;

class CompanyTransformer
{
    public function toOutputDTO(Company $company): CompanyOutputDTO
    {
        $dto = new CompanyOutputDTO();
        $dto->id = $company->getId();
        $dto->name = $company->getName();

        $dto->projects = $company->getProjects()
            ->map(fn($p) => [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'is_active' => $p->getIsActive(),
            ])
            ->toArray();

        return $dto;
    }
}


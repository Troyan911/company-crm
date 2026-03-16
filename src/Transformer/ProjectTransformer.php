<?php

namespace App\Transformer;

use App\DTO\Project\ProjectOutputDTO;
use App\Entity\Project;

class ProjectTransformer
{
    public function toOutputDTO(Project $project): ProjectOutputDTO
    {
        $dto = new ProjectOutputDTO();

        $dto->id = $project->getId();
        $dto->name = $project->getName();
        $dto->isActive = $project->getIsActive();

        $dto->companyId = $project->getCompany()?->getId();

        $dto->users = $project->getUsers()
            ->map(fn($u) => [
                'id' => $u->getId(),
                'email' => $u->getEmail(),
            ])
            ->toArray();

        return $dto;
    }
}

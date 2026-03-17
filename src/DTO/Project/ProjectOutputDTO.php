<?php

namespace App\DTO\Project;

class ProjectOutputDTO
{
    public ?int $id = null;

    public ?string $name = null;

    public ?bool $isActive = null;

    public ?int $companyId = null;

    public ?array $users = null;
}

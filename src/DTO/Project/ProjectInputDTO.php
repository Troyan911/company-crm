<?php

namespace App\DTO\Project;

use Symfony\Component\Validator\Constraints as Assert;

class ProjectInputDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public ?string $name = null;

    #[Assert\NotNull]
    public ?bool $isActive = true;

    #[Assert\NotNull]
    public ?int $companyId = null;
}

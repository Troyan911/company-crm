<?php

namespace App\DTO;

use App\Entity\Company;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyDTO
{
    public function __construct(?Company $company = null)
    {
        if ($company) {
            $this->id = $company->getId();
            $this->name = $company->getName();

            $this->projects = $company->getProjects()->map(fn($p) => [
                'id' => $p->getId(),
                'name' => $p->getName(),
                'is_active' => $p->getIsActive(),
            ])->toArray();
        }
    }

    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $name = null;

    public ?array $projects = null;
}

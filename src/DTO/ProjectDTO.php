<?php


namespace App\DTO;

use App\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;

class ProjectDTO
{
    public ?int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $name = null;

    public bool $isActive;

    #[Assert\NotBlank]
    public int $companyId;


    public function __construct(?Project $project = null)
    {
        if ($project) {
            $this->id = $project->getId();
            $this->name = $project->getName();
            $this->isActive = $project->getIsActive();
            $this->companyId = $project->getCompany()->getId();
        }
    }
}

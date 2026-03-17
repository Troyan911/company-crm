<?php

namespace App\DTO\Company;

use Symfony\Component\Validator\Constraints as Assert;

class CompanyInputDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $name = null,
    )
    {
    }
}

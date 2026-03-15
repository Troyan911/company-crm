<?php

namespace App\DTO\Login;

use Symfony\Component\Validator\Constraints as Assert;

class LoginInputDTO
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $password = null;
}

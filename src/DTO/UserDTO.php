<?php

namespace App\DTO;

use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(min: 3, max: 255)]
    public ?string $email = null;

    public ?int $projectId = null;


    public function __construct(?User $user = null)
    {
        if ($user) {
            $this->id = $user->getId();
            $this->firstName = $user->getFirstName();
            $this->lastName = $user->getLastName();
            $this->email = $user->getEmail();

            if ($user->getProject()) {
                $this->projectId = $user->getProject()->getId();
            }
        }
    }
}

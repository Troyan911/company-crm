<?php

namespace App\Transformer;

namespace App\Transformer;

use App\Entity\User;
use App\DTO\User\UserOutputDTO;

class UserTransformer
{
    public function toOutputDTO(User $user): UserOutputDTO
    {
        $dto = new UserOutputDTO();

        $dto->id = $user->getId();
        $dto->firstName = $user->getFirstName();
        $dto->lastName = $user->getLastName();
        $dto->email = $user->getEmail();

        $dto->projectId = $user->getProject()?->getId();

        return $dto;
    }
}


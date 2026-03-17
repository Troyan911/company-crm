<?php

namespace App\Service;

use App\DTO\Login\LoginInputDTO;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class AuthService
{
    public function __construct(
        private UserRepository              $repo,
        private UserPasswordHasherInterface $hasher,
        private JWTTokenManagerInterface    $jwt,
    )
    {
    }

    public function login(LoginInputDTO $dto): string
    {
        $user = $this->repo->findByEmail($dto->email);

        if (!$user) {
            throw new UnauthorizedHttpException('', 'Invalid credentials');
        }

        if (!$this->hasher->isPasswordValid($user, $dto->password)) {
            throw new UnauthorizedHttpException('', 'Invalid credentials');
        }

        return $this->jwt->create($user);
    }
}

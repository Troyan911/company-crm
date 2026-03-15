<?php

namespace App\Controller\Api;

use App\DTO\Login\LoginInputDTO;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthService        $authService,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('/api/login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new LoginInputDTO();
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $token = $this->authService->login($dto);

        return $this->json([
            'token' => $token
        ]);
    }
}

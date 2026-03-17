<?php

namespace App\Controller\Api;

use App\DTO\Login\LoginInputDTO;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth')]
class LoginController extends AbstractController
{
    public function __construct(
        private readonly AuthService        $authService,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('/api/login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/login',
        summary: 'User login',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'email',
                        type: 'string',
                        example: 'admin@mail.com'
                    ),
                    new OA\Property(
                        property: 'password',
                        type: 'string',
                        example: '123456'
                    ),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'JWT token',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'token',
                            type: 'string'
                        )
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Validation error'
            ),
            new OA\Response(
                response: 401,
                description: 'Invalid credentials'
            )
        ]
    )]
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

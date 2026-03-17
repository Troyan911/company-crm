<?php

namespace App\Controller\Api;

use App\DTO\User\UserInputDTO;
use App\Service\UserService;
use App\Transformer\UserTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/user')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService        $service,
        private readonly UserTransformer    $transformer,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        path: '/api/user',
        summary: 'Get all users',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of users',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'firstName', type: 'string'),
                            new OA\Property(property: 'lastName', type: 'string'),
                            new OA\Property(property: 'email', type: 'string'),
                            new OA\Property(property: 'projectId', type: 'integer'),
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $users = $this->service->list();

        $data = array_map(
            fn($u) => $this->transformer->toOutputDTO($u),
            $users
        );

        return $this->json($data);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[OA\Get(
        path: '/api/user/{id}',
        summary: 'Get user by ID',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'User object',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'firstName', type: 'string'),
                        new OA\Property(property: 'lastName', type: 'string'),
                        new OA\Property(property: 'email', type: 'string'),
                        new OA\Property(property: 'projectId', type: 'integer'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $user = $this->service->findOrFail($id);

        return $this->json(
            $this->transformer->toOutputDTO($user)
        );
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        path: '/api/user',
        summary: 'Create user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'projectId', type: 'integer'),
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Created'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new UserInputDTO();
        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->projectId = $data['projectId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $user = $this->service->create($dto);

        return $this->json(
            $this->transformer->toOutputDTO($user),
            201
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[OA\Put(
        path: '/api/user/{id}',
        summary: 'Update user',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'firstName', type: 'string'),
                    new OA\Property(property: 'lastName', type: 'string'),
                    new OA\Property(property: 'email', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'projectId', type: 'integer'),
                ],
                type: 'object'
            )
        ),
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Updated'),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new UserInputDTO();
        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->projectId = $data['projectId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $user = $this->service->update($id, $dto);

        return $this->json(
            $this->transformer->toOutputDTO($user)
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/user/{id}',
        summary: 'Delete user',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Deleted'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json([
            'status' => 'deleted'
        ]);
    }
}

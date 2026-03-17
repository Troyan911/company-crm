<?php

namespace App\Controller\Api;

use App\DTO\Project\ProjectInputDTO;
use App\Service\ProjectService;
use App\Transformer\ProjectTransformer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/project')]
#[OA\Tag(name: 'Project')]
class ProjectController extends BaseController
{
    public function __construct(
        ProjectService                      $service,
        ProjectTransformer                  $transformer,
        private readonly ValidatorInterface $validator
    )
    {
        $this->init($service, $transformer);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        path: '/api/project',
        summary: 'Get all projects',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of projects',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
                            new OA\Property(property: 'isActive', type: 'boolean'),
                            new OA\Property(property: 'companyId', type: 'integer')
                        ],
                        type: 'object'
                    )
                )
            )
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        return $this->paginatedList($request);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    #[OA\Get(
        path: '/api/project/{id}',
        summary: 'Get project by ID',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Project object',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                        new OA\Property(property: 'isActive', type: 'boolean'),
                        new OA\Property(property: 'companyId', type: 'integer')
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Project not found')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $project = $this->service->findOrFail($id);

        return $this->json($this->transformer->toOutputDTO($project));
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        path: '/api/project',
        summary: 'Create a project',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'isActive', type: 'boolean'),
                    new OA\Property(property: 'companyId', type: 'integer')
                ],
                type: 'object'
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Project created'),
            new OA\Response(response: 400, description: 'Validation errors')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ProjectInputDTO();
        $dto->name = $data['name'] ?? null;
        $dto->isActive = $data['isActive'] ?? true;
        $dto->companyId = $data['companyId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $project = $this->service->create($dto);

        return $this->json($this->transformer->toOutputDTO($project), 201);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[OA\Put(
        path: '/api/project/{id}',
        summary: 'Update a project',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
                    new OA\Property(property: 'isActive', type: 'boolean'),
                    new OA\Property(property: 'companyId', type: 'integer')
                ],
                type: 'object'
            )
        ),
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Project updated'),
            new OA\Response(response: 400, description: 'Validation errors'),
            new OA\Response(response: 404, description: 'Project not found')
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ProjectInputDTO();
        $dto->name = $data['name'] ?? null;
        $dto->isActive = $data['isActive'] ?? true;
        $dto->companyId = $data['companyId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $project = $this->service->update($id, $dto);

        return $this->json($this->transformer->toOutputDTO($project));
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/project/{id}',
        summary: 'Delete a project',
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Project deleted'),
            new OA\Response(response: 404, description: 'Project not found')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json(['status' => 'deleted']);
    }
}

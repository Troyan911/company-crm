<?php

namespace App\Controller\Api;

use App\DTO\Company\CompanyInputDTO;
use App\Transformer\CompanyTransformer;
use App\Service\CompanyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;

#[Route('/api/company')]
#[OA\Tag(name: 'Company')]
class CompanyController extends BaseController
{
    public function __construct(
        CompanyService                      $service,
        CompanyTransformer                  $transformer,
        private readonly ValidatorInterface $validator
    )
    {
        $this->init($service, $transformer);
    }

    #[Route('', methods: ['GET'])]
    #[OA\Get(
        path: '/api/company',
        summary: 'Get all companies',
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of companies',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer'),
                            new OA\Property(property: 'name', type: 'string'),
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
        path: '/api/company/{id}',
        summary: 'Get company by ID',
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
                description: 'Company object',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer'),
                        new OA\Property(property: 'name', type: 'string'),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function get(int $id): JsonResponse
    {
        $company = $this->service->findOrFail($id);

        return $this->json($this->transformer->toOutputDTO($company));
    }

    #[Route('', methods: ['POST'])]
    #[OA\Post(
        path: '/api/company',
        summary: 'Create company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
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

        $dto = new CompanyInputDTO();
        $dto->name = $data['name'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $company = $this->service->create($dto);

        return $this->json($this->transformer->toOutputDTO($company), 201);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
    #[OA\Put(
        path: '/api/company/{id}',
        summary: 'Update company',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string'),
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

        $dto = new CompanyInputDTO();
        $dto->name = $data['name'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $company = $this->service->update($id, $dto);

        return $this->json($this->transformer->toOutputDTO($company));
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/company/{id}',
        summary: 'Delete company',
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

        return $this->json(['status' => 'deleted']);
    }
}

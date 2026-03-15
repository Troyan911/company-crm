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

#[Route('/api/project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectService     $service,
        private readonly ProjectTransformer $transformer,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $projects = $this->service->list();

        $data = array_map(
            fn($p) => $this->transformer->toOutputDTO($p),
            $projects
        );

        return $this->json($data);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $project = $this->service->findOrFail($id);

        return $this->json(
            $this->transformer->toOutputDTO($project)
        );
    }

    #[Route('', methods: ['POST'])]
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

        return $this->json(
            $this->transformer->toOutputDTO($project),
            201
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['PUT'])]
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

        return $this->json(
            $this->transformer->toOutputDTO($project)
        );
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json([
            'status' => 'deleted'
        ]);
    }
}

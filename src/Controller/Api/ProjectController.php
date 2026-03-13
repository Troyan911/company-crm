<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\DTO\ProjectDTO;
use App\Service\ProjectService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/project')]
class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectService $service,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface $validator
    ) {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $projects = $this->em
            ->getRepository(Project::class)
            ->findAll();

        $dto = array_map(
            fn($p) => new ProjectDTO($p),
            $projects
        );

        return $this->json($dto);
    }


    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getProject(int $id): JsonResponse
    {
        $project = $this->service->findOrFail($id);

        return $this->json(new ProjectDTO($project));
    }


    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ProjectDTO();
        $dto->name = $data['name'] ?? null;
        $dto->isActive = $data['isActive'] ?? true;
        $dto->companyId = $data['companyId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $project = $this->service->create($dto);

        return $this->json(new ProjectDTO($project), 201);
    }


    #[Route('/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $dto = new ProjectDTO();
        $dto->name = $data['name'] ?? null;
        $dto->isActive = $data['isActive'] ?? true;
        $dto->companyId = $data['companyId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $project = $this->service->update($id, $dto);

        return $this->json(new ProjectDTO($project));
    }


    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json([
            'status' => 'deleted'
        ]);
    }
}

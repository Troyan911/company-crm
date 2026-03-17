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

#[Route('/api/company')]
class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService     $service,
        private readonly CompanyTransformer $transformer,
        private readonly ValidatorInterface $validator
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $companies = $this->service->list();
        $data = array_map(fn($c) => $this->transformer->toOutputDTO($c), $companies);

        return $this->json($data);
    }

    #[Route('/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function get(int $id): JsonResponse
    {
        $company = $this->service->findOrFail($id);

        return $this->json($this->transformer->toOutputDTO($company));
    }

    #[Route('', methods: ['POST'])]
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
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json(['status' => 'deleted']);
    }
}

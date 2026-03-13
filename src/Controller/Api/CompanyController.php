<?php

namespace App\Controller\Api;

use App\Entity\Company;
use App\DTO\CompanyDTO;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/company')]
class CompanyController extends AbstractController
{
    public function __construct(
        private readonly CompanyService         $service,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $companies = $this->em->getRepository(Company::class)->findAll();
        $companiesDto = array_map(fn($c) => new CompanyDTO($c), $companies);

        return $this->json($companiesDto);
    }

    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getCompany(int $id, CompanyService $companyService): JsonResponse
    {
        $company = $companyService->findOrFail($id);

        return $this->json(new CompanyDTO($company));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new CompanyDTO();
        $dto->name = $data['name'] ?? null;

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $company = $this->service->create($dto);

        return $this->json($company, 201);
    }

    #[Route('/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = new CompanyDTO();
        $dto->name = $data['name'] ?? null;

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }
        $company = $this->service->update($id, $dto);

        return $this->json($company);
    }

    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json(['status' => 'deleted']);
    }
}

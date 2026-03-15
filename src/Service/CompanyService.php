<?php

namespace App\Service;

use App\DTO\Company\CompanyInputDTO;
use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CompanyRepository      $repository
    )
    {
    }

    public function create(CompanyInputDTO $dto): Company
    {
        $company = new Company();
        $company->setName($dto->name);

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    public function update(int $id, CompanyInputDTO $dto): Company
    {
        $company = $this->findOrFail($id);
        $company->setName($dto->name);
        $this->em->flush();

        return $company;
    }

    public function delete(int $id): void
    {
        $company = $this->findOrFail($id);
        $this->em->remove($company);
        $this->em->flush();
    }

    public function findOrFail(int $id): Company
    {
        $company = $this->repository->findCompany($id);

        if (!$company) {
            throw new NotFoundHttpException('Company not found');
        }

        return $company;
    }

    public function list(): array
    {
        return $this->repository->findAllCompanies();
    }
}

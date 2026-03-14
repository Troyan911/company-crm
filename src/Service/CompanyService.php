<?php

namespace App\Service;

use App\Dto\CompanyDTO;
use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CompanyService
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function create(CompanyDTO $dto): Company
    {
        $company = new Company();
        $company->setName($dto->name);

        $this->em->persist($company);
        $this->em->flush();

        return $company;
    }

    public function update(int $id, CompanyDTO $dto): Company
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
        $entity = $this->em->getRepository(Company::class)->find($id);

        if (!$entity) {
            throw new NotFoundHttpException('Company not found');
        }

        return $entity;
    }
}

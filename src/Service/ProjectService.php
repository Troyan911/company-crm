<?php

namespace App\Service;

use App\DTO\ProjectDTO;
use App\Entity\Company;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function create(ProjectDTO $dto): Project
    {
        $company = $this->em
            ->getRepository(Company::class)
            ->find($dto->companyId);

        if (!$company) {
            throw new NotFoundHttpException("Company not found");
        }

        $project = new Project();

        $project
            ->setName($dto->name)
            ->setIsActive($dto->isActive)
            ->setCompany($company);

        $this->em->persist($project);
        $this->em->flush();

        return $project;
    }


    public function update(int $id, ProjectDTO $dto): Project
    {
        $project = $this->em
            ->getRepository(Project::class)
            ->find($id);

        if (!$project) {
            throw new NotFoundHttpException("Project not found");
        }

        $company = $this->em
            ->getRepository(Company::class)
            ->find($dto->companyId);

        if (!$company) {
            throw new NotFoundHttpException("Company not found");
        }

        $project
            ->setName($dto->name)
            ->setIsActive($dto->isActive)
            ->setCompany($company);

        $this->em->flush();

        return $project;
    }


    public function delete(int $id): void
    {
        $project = $this->em
            ->getRepository(Project::class)
            ->find($id);

        if (!$project) {
            throw new NotFoundHttpException();
        }

        $this->em->remove($project);
        $this->em->flush();
    }

    public function findOrFail(int $id): Project
    {
        $entity = $this->em->getRepository(Project::class)->find($id);

        if (!$entity) {
            throw new NotFoundHttpException('Project not found');
        }

        return $entity;
    }
}

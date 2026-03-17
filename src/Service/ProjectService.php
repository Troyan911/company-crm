<?php

namespace App\Service;

use App\DTO\Project\ProjectInputDTO;
use App\Entity\Company;
use App\Entity\Project;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectService extends BaseService
{
    public function __construct(
        EntityManagerInterface $em,
        ProjectRepository      $repository
    )
    {
        parent::__construct($em, $repository);
    }

    public function list(): array
    {
        return $this->repository->findAllProjects();
    }

    public function findOrFail(int $id): Project
    {
        $project = $this->repository->findProject($id);

        if (!$project) {
            throw new NotFoundHttpException("Project not found");
        }

        return $project;
    }

    public function create(ProjectInputDTO $dto): Project
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

    public function update(int $id, ProjectInputDTO $dto): Project
    {
        $project = $this->findOrFail($id);

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
        $project = $this->findOrFail($id);

        $this->em->remove($project);
        $this->em->flush();
    }
}

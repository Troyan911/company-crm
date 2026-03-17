<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Persistence\ManagerRegistry;

class ProjectRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findAllProjects(): array
    {
        return $this->findAll();
    }

    public function findProject(int $id): ?Project
    {
        return $this->find($id);
    }
}

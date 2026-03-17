<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Pagerfanta;

abstract class BaseService
{
    protected function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly object                 $repository
    )
    {
    }

    public function listPaginated(int $page, int $limit): Pagerfanta
    {
        return $this->repository->paginate($page, $limit);
    }
}

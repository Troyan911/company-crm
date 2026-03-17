<?php

namespace App\Service;

use App\DTO\User\UserInputDTO;
use App\Entity\Project;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserRepository              $repository,
        private readonly UserPasswordHasherInterface $hasher,
    )
    {
    }

    public function list(): array
    {
        return $this->repository->findAllUsers();
    }

    public function findOrFail(int $id): User
    {
        $user = $this->repository->findUser($id);

        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }

        return $user;
    }

    public function create(UserInputDTO $dto): User
    {
        if ($this->repository->findByEmail($dto->email)) {
            throw new BadRequestHttpException('Email already exists');
        }

        $user = new User();

        $user
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setEmail($dto->email);

        if ($dto->password) {
            $hash = $this->hasher->hashPassword(
                $user,
                $dto->password
            );

            $user->setPassword($hash);
        }

        if ($dto->projectId) {

            $project = $this->em
                ->getRepository(Project::class)
                ->find($dto->projectId);

            if (!$project) {
                throw new NotFoundHttpException("Project not found");
            }

            $user->setProject($project);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function update(int $id, UserInputDTO $dto): User
    {
        $user = $this->findOrFail($id);

        // check email change
        if ($dto->email && $dto->email !== $user->getEmail()) {

            $existing = $this->repository->findOneByEmail($dto->email);

            if ($existing && $existing->getId() !== $user->getId()) {
                throw new BadRequestHttpException('Email already exists');
            }

            $user->setEmail($dto->email);
        }

        $user
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName);

        if ($dto->password) {

            $hash = $this->hasher->hashPassword(
                $user,
                $dto->password
            );

            $user->setPassword($hash);
        }

        if ($dto->projectId) {

            $project = $this->em
                ->getRepository(Project::class)
                ->find($dto->projectId);

            if (!$project) {
                throw new NotFoundHttpException("Project not found");
            }

            $user->setProject($project);
        }

        $this->em->flush();

        return $user;
    }

    public function delete(int $id): void
    {
        $user = $this->findOrFail($id);

        $this->em->remove($user);
        $this->em->flush();
    }
}

<?php

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\Project;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private EntityManagerInterface      $em,
        private UserPasswordHasherInterface $hasher,
    )
    {
    }

    public function create(UserDTO $dto): User
    {
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

        // project

        if ($dto->projectId) {

            $project = $this->em
                ->getRepository(Project::class)
                ->find($dto->projectId);

            if (!$project) {
                throw new NotFoundHttpException();
            }

            $user->setProject($project);
        }

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }


    public function update(int $id, UserDTO $dto): User
    {
        $user = $this->findOrFail($id);

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

        $this->em->flush();

        return $user;
    }


    public function delete(int $id): void
    {
        $user = $this->findOrFail($id);

        $this->em->remove($user);
        $this->em->flush();
    }

    public function findOrFail(int $id): User
    {
        $user = $this->em
            ->getRepository(User::class)
            ->find($id);

        if (!$user) {
            throw new NotFoundHttpException("User not found");
        }

        return $user;
    }

}

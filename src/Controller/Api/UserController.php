<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\DTO\UserDTO;
use App\Repository\UserRepository;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/user')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService            $service,
        private readonly EntityManagerInterface $em,
        private readonly ValidatorInterface     $validator
    )
    {
    }

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $users = $this->em
            ->getRepository(User::class)
            ->findAll();

        $dto = array_map(
            fn($u) => new UserDTO($u),
            $users
        );

        return $this->json($dto);
    }


    #[Route('/{id}', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function getUserData(int $id): JsonResponse
    {
        $user = $this->service->findOrFail($id);

        return $this->json(new UserDTO($user));
    }


    #[Route('', methods: ['POST'])]
    public function create(
        Request        $request,
        UserRepository $userRepository
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return new JsonResponse([
                'error' => 'Email already exists'
            ], 400);
        }

        $dto = new UserDTO();

        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->projectId = $data['projectId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $user = $this->service->create($dto);

        return $this->json(
            new UserDTO($user),
            201
        );
    }


    #[Route('/{id}', methods: ['PUT'], requirements: ['id' => '\d+'])]
    public function update(
        Request $request,
        int     $id
    ): JsonResponse
    {

        $data = json_decode($request->getContent(), true);

        $dto = new UserDTO();

        $dto->firstName = $data['firstName'] ?? null;
        $dto->lastName = $data['lastName'] ?? null;
        $dto->email = $data['email'] ?? null;
        $dto->password = $data['password'] ?? null;
        $dto->projectId = $data['projectId'] ?? null;

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $user = $this->service->update($id, $dto);

        return $this->json(
            new UserDTO($user)
        );
    }


    #[Route('/{id}', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(int $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->json([
            'status' => 'deleted'
        ]);
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {

    }

    public function load(ObjectManager $manager): void
    {
        $project = $this->getReference(ProjectFixtures::PROJECT_1, Project::class);
        $defaultPassword = 'secret';

        for ($i = 1; $i <= 5; $i++) {

            $user = (new User())
                ->setFirstName('User' . $i)
                ->setLastName('Test')
                ->setEmail("user$i@test.com")
                ->setProject($project);

            $user->setPassword($this->passwordHasher->hashPassword($user, $defaultPassword));


            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }
}

<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;


class ProjectFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROJECT_1 = 'project_1';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $keys = array_keys(CompanyFixtures::COMPANIES);
            $companyIndex = $i % count($keys);
            $companyRef = $keys[$companyIndex];
            $company = $this->getReference($companyRef, Company::class);

            $project = (new Project())
                ->setName($faker->word() . " project")
                ->setIsActive(true)
                ->setCompany($company);

            $manager->persist($project);
        }

        $this->addReference(self::PROJECT_1, $project);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }
}

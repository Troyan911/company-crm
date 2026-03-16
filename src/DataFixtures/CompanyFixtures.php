<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service_locator;

class CompanyFixtures extends Fixture
{
    public const COMPANIES = [
        'company_1' => 'Google',
        'company_2' => 'Amazon'
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::COMPANIES as $key => $name) {
            $company = (new Company())
                ->setName($name);
            $manager->persist($company);
            $this->addReference($key, $company);
        }

        $manager->flush();
    }
}

<?php

namespace App\Tests\Unit;

use App\Entity\Company;
use App\Entity\Project;
use PHPUnit\Framework\TestCase;

class CompanyEntityTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $company = new Company();

        // Test name setter/getter
        $company->setName('Acme Corp');
        $this->assertSame('Acme Corp', $company->getName());

        // ID should be null before persisting
        $this->assertNull($company->getId());
    }

    public function testProjectsCollection(): void
    {
        $company = new Company();
        $project1 = new Project();
        $project2 = new Project();

        $project1->setName('Project 1');
        $project2->setName('Project 2');

        // Add projects
        $company->addProject($project1);
        $company->addProject($project2);

        $projects = $company->getProjects();

        $this->assertCount(2, $projects);
        $this->assertTrue($projects->contains($project1));
        $this->assertTrue($projects->contains($project2));

        // Test remove
        $company->removeProject($project1);
        $this->assertCount(1, $company->getProjects());
        $this->assertFalse($projects->contains($project1));
        $this->assertTrue($projects->contains($project2));

        // Check owning side is updated
        $this->assertNull($project1->getCompany());
        $this->assertSame($company, $project2->getCompany());
    }
}

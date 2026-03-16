<?php

namespace App\Tests\Unit;

use App\Entity\Company;
use App\Entity\Project;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ProjectEntityTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $project = new Project();

        // Test name setter/getter
        $project->setName('Website Redesign');
        $this->assertSame('Website Redesign', $project->getName());

        // Test isActive
        $project->setIsActive(true);
        $this->assertTrue($project->getIsActive());

        // Test company relation
        $company = new Company();
        $project->setCompany($company);
        $this->assertSame($company, $project->getCompany());

        // ID should be null before persisting
        $this->assertNull($project->getId());
    }

    public function testUsersCollection(): void
    {
        $project = new Project();
        $user1 = new User();
        $user2 = new User();

        $user1->setFirstName('John');
        $user2->setFirstName('Alice');

        // Add users
        $project->addUser($user1);
        $project->addUser($user2);

        $users = $project->getUsers();

        $this->assertCount(2, $users);
        $this->assertTrue($users->contains($user1));
        $this->assertTrue($users->contains($user2));

        // Remove user
        $project->removeUser($user1);
        $this->assertCount(1, $project->getUsers());
        $this->assertFalse($users->contains($user1));
        $this->assertTrue($users->contains($user2));

        // Check owning side is updated
        $this->assertNull($user1->getProject());
        $this->assertSame($project, $user2->getProject());
    }
}

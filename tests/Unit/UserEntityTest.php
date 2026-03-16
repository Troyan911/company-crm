<?php

namespace App\Tests\Unit;

use App\Entity\Project;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $user->setFirstName('Jane');
        $user->setLastName('Doe');
        $user->setEmail('jane.doe@example.com');
        $user->setPassword('secret');

        $this->assertSame('Jane', $user->getFirstName());
        $this->assertSame('Doe', $user->getLastName());
        $this->assertSame('jane.doe@example.com', $user->getEmail());
        $this->assertSame('secret', $user->getPassword());

        // Test project relation
        $project = new Project();
        $user->setProject($project);
        $this->assertSame($project, $user->getProject());

        // ID should be null before persisting
        $this->assertNull($user->getId());
    }
}

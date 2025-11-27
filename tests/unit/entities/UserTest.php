<?php

/**By Kelian */

declare(strict_types=1);

namespace Tests\unit\entities;

use App\entities\User;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase 
{
    private User $parent;

    protected function setUp(): void 
    {
        $this->parent = new User('1', 'kelian@example.com', "Kelian");
    }

    public function testGetEmail(): void 
    {
        $this->assertEquals('kelian@example.com', $this->parent->getEmail());
    }

    public function testGetName(): void
    {
        $this->assertEquals("Kelian", $this->parent->getName());
    }
}
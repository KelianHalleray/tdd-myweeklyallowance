<?php

declare(strict_types=1);

namespace Tests\unit\entities;

use App\entities\Allocation;
use App\entities\Account;
use App\entities\User;
use PHPUnit\Framework\TestCase;

final class AllocationTest extends TestCase
{
    private Allocation $allocation;
    private Account $account;
    private User $parent;

    protected function setUp(): void
    {
        $this->parent = new User('1', 'kelian@example.com', 'Kelian');
        $this->account = new Account('1', 'Owen', $this->parent);
        $this->allocation = new Allocation('1', $this->account, 10, 0);
    }

    public function testGetId(): void
    {
        $this->assertEquals('1', $this->allocation->getId());
    }

    public function testGetAccount(): void
    {
        $this->assertSame($this->account, $this->allocation->getAccount());
    }

    public function testGetWeeklyAmount(): void
    {
        $this->assertEquals(10, $this->allocation->getWeeklyAmount());
    }

    public function testSetWeeklyAmount(): void
    {
        $this->allocation->setWeeklyAmount(20);
        $this->assertEquals(20, $this->allocation->getWeeklyAmount());
    }

    public function testGetDayOfWeek(): void
    {
        $this->assertEquals(0, $this->allocation->getDayOfWeek());
    }


}
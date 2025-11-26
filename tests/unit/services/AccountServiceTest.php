<?php

namespace App\Tests\unit\services;

use App\entities\User;
use App\services\AccountService;
use PHPUnit\Framework\TestCase;

final class AccountServiceTest extends TestCase
{
    private AccountService $accountService;

    protected function setUp(): void
    {
        $this->accountService = new AccountService();
    }

    public function testCreateAccountWithInitialBalance(): void
    {
        $parent = new User('1', 'kelian@example.com', 'kelian');
        
        $account = $this->accountService->createAccount($parent, 'Owen', 50);

        $this->assertEquals('Alice', $account->getAdolescentName());
        $this->assertEquals(50, $account->getBalance());
        $this->assertSame($parent, $account->getParent());
    }

    public function testCreateAccountWithoutInitialBalance(): void
    {
        $parent = new User('1', 'kelian@example.com', 'kelian');
        
        $account = $this->accountService->createAccount($parent, 'Owen');

        $this->assertEquals(0, $account->getBalance());
    }

    public function testUpdateBalance(): void
    {
        $parent = new User('1', 'kelian@example.com', 'kelian');
        $account = $this->accountService->createAccount($parent, 'Owen', 50);

        $this->accountService->updateBalance($account, 150);

        $this->assertEquals(150, $account->getBalance());
    }
}
<?php

/**By Kelian */

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

        $this->assertEquals('Owen', $account->getAdolescentName());
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

    public function testCreateAccountWithNegativeBalanceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("La balance ne peut être négative.");

        $parent = new User('1', 'kelian@example.com', 'kelian');
        $this->accountService->createAccount($parent, 'Owen', -10);
    }

    public function testCreateAccountWithEmptyNameThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Le nom ne peut être vide.");

        $parent = new User('1', 'kelian@example.com', 'kelian');
        $this->accountService->createAccount($parent, '');
    }

    public function testCreateAccountGeneratesId(): void
    {
        $parent = new User('1', 'kelian@example.com', 'kelian');
        
        $account = $this->accountService->createAccount($parent, 'Owen');

        $this->assertNotNull($account->getId());
        $this->assertIsString($account->getId());
    }


    public function testGetAccountsByParent(): void
    {
        $parent1 = new User('1', 'kelian@example.com', 'kelian');
        $parent2 = new User('2', 'owen@example.com', 'owen');

        $account1 = $this->accountService->createAccount($parent1, 'Owen');
        $account2 = $this->accountService->createAccount($parent1, 'Sophie');
        $account3 = $this->accountService->createAccount($parent2, 'Emma');

        $accountsParent1 = $this->accountService->getAccountsByParent($parent1);

        $this->assertCount(2, $accountsParent1);
        $this->assertContains($account1, $accountsParent1);
        $this->assertContains($account2, $accountsParent1);
        $this->assertNotContains($account3, $accountsParent1);
    }

    public function testGetAccountsByParentWithNoAccounts(): void
    {
        $parent = new User('1', 'kelian@example.com', 'kelian');

        $accounts = $this->accountService->getAccountsByParent($parent);

        $this->assertIsArray($accounts);
        $this->assertEmpty($accounts);
    }
}
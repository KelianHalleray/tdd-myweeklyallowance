<?php

/**By Kelian */

declare(strict_types=1);

namespace Tests\unit\entities;

use App\entities\Account;
use App\entities\User;
use App\entities\Transaction;
use App\entities\Allocation;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    private User $parent;
    private Account $account;

    protected function setUp(): void
    {
        $this->parent = new User('1', 'kelian@example.com', 'Kelian');
        $this->account = new Account('1', 'Owen', $this->parent);
    }

    public function testGetId(): void
    {
        $this->assertEquals('1', $this->account->getId());
    }

    public function testGetAdolescentName(): void
    {
        $this->assertEquals('Owen', $this->account->getAdolescentName());
    }

    public function testGetParent(): void
    {
        $this->assertSame($this->parent, $this->account->getParent());
    }

    public function testGetBalanceInitializedToZero(): void
    {
        $this->assertEquals(0, $this->account->getBalance());
    }

    public function testSetBalance(): void
    {
        $this->account->setBalance(100);
        $this->assertEquals(100, $this->account->getBalance());
    }

    public function testGetWeeklyAllocationInitializedToZero(): void
    {
        $this->assertEquals(0, $this->account->getWeeklyAllocation());
    }

    public function testSetWeeklyAllocation(): void
    {
        $this->account->setWeeklyAllocation(10);
        $this->assertEquals(10, $this->account->getWeeklyAllocation());
    }

    public function testAddTransaction(): void
    {
        $transaction = new Transaction(
            '1',
            $this->account,
            Transaction::TYPE_DEPOSIT,
            50,
            'Test description'
        );

        $this->account->addTransaction($transaction);
        $transactions = $this->account->getTransactions();

        $this->assertCount(1, $transactions);
        $this->assertSame($transaction, $transactions[0]);
    }

    public function testGetTransactionsEmptyInitially(): void
    {
        $this->assertEmpty($this->account->getTransactions());
    }

    public function testAddMultipleTransactions(): void
    {
        $tr1 = new Transaction('1', $this->account, Transaction::TYPE_DEPOSIT, 50);
        $tr2 = new Transaction('2', $this->account, Transaction::TYPE_EXPENSE, 20);

        $this->account->addTransaction($tr1);
        $this->account->addTransaction($tr2);

        $this->assertCount(2, $this->account->getTransactions());
    }

    public function testGetAllocationInitiallyNull(): void
    {
        $this->assertNull($this->account->getAllocation());
    }

    public function testSetAllocation(): void
    {
        $allocation = new Allocation(
            '1',
            $this->account,
            10,
            0
        );

        $this->account->setAllocation($allocation);
        $this->assertSame($allocation, $this->account->getAllocation());
    }
}
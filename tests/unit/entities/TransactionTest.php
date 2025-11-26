<?php

declare(strict_types=1);

namespace Tests\unit\entities;

use App\entities\Account;
use App\entities\User;
use App\entities\Transaction;
use PHPUnit\Framework\TestCase;

final class TransactionTest extends TestCase
{
    private User $parent;
    private Account $account;
    private Transaction $transaction;

    protected function setUp(): void
    {
        $this->parent = new User('1', 'kelian@example.com', 'Kelian');
        $this->account = new Account('1', 'Owen', $this->parent);
        $this->transaction = new Transaction(
            '1',
            $this->account,
            Transaction::TYPE_DEPOSIT,
            50,
            'Test description'
        );
    }

    public function testGetId(): void
    {
        $this->assertEquals('1', $this->transaction->getId());
    }

    public function testGetAccount(): void
    {
        $this->assertSame($this->account, $this->transaction->getAccount());
    }

    public function testGetType(): void
    {
        $this->assertEquals(Transaction::TYPE_DEPOSIT, $this->transaction->getType());
    }

    public function testGetAmount(): void
    {
        $this->assertEquals(50, $this->transaction->getAmount());
    }

    public function testGetDescription(): void
    {
        $this->assertEquals('Test description', $this->transaction->getDescription());
    }

    public function testGetDate(): void
    {
        $this->assertInstanceOf(\DateTime::class, $this->transaction->getDate());
    }

    public function testCreateExpenseTransaction(): void
    {
        $expense = new Transaction(
            '2',
            $this->account,
            Transaction::TYPE_EXPENSE,
            25,
            'Test achat'
        );

        $this->assertEquals(Transaction::TYPE_EXPENSE, $expense->getType());
        $this->assertEquals(25, $expense->getAmount());
    }
}
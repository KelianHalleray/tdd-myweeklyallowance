<?php

/**By Kelian */

declare(strict_types=1);

namespace App\Tests\unit\services;

use App\entities\Account;
use App\entities\User;
use App\entities\Transaction;
use App\services\AccountService;
use App\services\TransactionService;
use PHPUnit\Framework\TestCase;

final class TransactionServiceTest extends TestCase
{
    private TransactionService $transactionService;
    private AccountService $accountService;
    private Account $account;

    protected function setUp(): void
    {
        $this->accountService = new AccountService();
        $this->transactionService = new TransactionService($this->accountService);

        $parent = new User('1', 'kelian@example.com', 'Kelian');
        $this->account = $this->accountService->createAccount($parent, 'Owen', 100);
    }

    public function testDepositIncreasesBalance(): void
    {
        $this->transactionService->deposit($this->account, 50);

        $this->assertEquals(150, $this->account->getBalance());
    }

    public function testDepositCreatesTransaction(): void
    {
        $transaction = $this->transactionService->deposit($this->account, 50);

        $this->assertEquals(Transaction::TYPE_DEPOSIT, $transaction->getType());
        $this->assertEquals(50, $transaction->getAmount());
    }

    public function testDepositNegativeAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->deposit($this->account, -50);
    }

    public function testDepositZeroAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->deposit($this->account, 0);
    }

    public function testWithdrawDecreasesBalance(): void
    {
        $this->transactionService->withdraw($this->account, 30);

        $this->assertEquals(70, $this->account->getBalance());
    }

    public function testWithdrawCreatesTransaction(): void
    {
        $transaction = $this->transactionService->withdraw($this->account, 30);

        $this->assertEquals(Transaction::TYPE_EXPENSE, $transaction->getType());
        $this->assertEquals(30, $transaction->getAmount());
    }

    public function testWithdrawInsufficientBalanceThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->withdraw($this->account, 200);
    }

    public function testWithdrawNegativeAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->withdraw($this->account, -30);
    }

    public function testWithdrawZeroAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->withdraw($this->account, 0);
    }

    public function testWithdrawExactBalance(): void
    {
        $this->transactionService->withdraw($this->account, 100);

        $this->assertEquals(0, $this->account->getBalance());
    }

    public function testWithdrawOneMoreThanBalance(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->transactionService->withdraw($this->account, 101);
    }

    public function testMultipleDepositsIncrementBalance(): void
    {
        $this->transactionService->deposit($this->account, 50);
        $this->transactionService->deposit($this->account, 25);

        $this->assertEquals(175, $this->account->getBalance());
    }

    public function testMultipleWithdrawsDecrementBalance(): void
    {
        $this->transactionService->withdraw($this->account, 20);
        $this->transactionService->withdraw($this->account, 30);

        $this->assertEquals(50, $this->account->getBalance());
    }

    public function testMixedTransactions(): void
    {
        $this->transactionService->deposit($this->account, 50);
        $this->transactionService->withdraw($this->account, 20);
        $this->transactionService->deposit($this->account, 30);

        $this->assertEquals(160, $this->account->getBalance());
    }

    public function testGetTotalExpenses(): void
    {
        $this->transactionService->withdraw($this->account, 20);
        $this->transactionService->withdraw($this->account, 30);
        $this->transactionService->deposit($this->account, 50);

        $total = $this->transactionService->getTotalExpenses($this->account);

        $this->assertEquals(50, $total);
    }

    public function testGetTotalDeposits(): void
    {
        $this->transactionService->deposit($this->account, 50);
        $this->transactionService->deposit($this->account, 30);
        $this->transactionService->withdraw($this->account, 20);

        $total = $this->transactionService->getTotalDeposits($this->account);

        $this->assertEquals(80, $total);
    }

    public function testGetTotalExpensesEmpty(): void
    {
        $total = $this->transactionService->getTotalExpenses($this->account);

        $this->assertEquals(0, $total);
    }

    public function testGetTotalDepositsEmpty(): void
    {
        $total = $this->transactionService->getTotalDeposits($this->account);

        $this->assertEquals(0, $total);
    }

    public function testGetTransactionsByAccount(): void
    {
        $this->transactionService->deposit($this->account, 50);
        $this->transactionService->withdraw($this->account, 20);

        $transactions = $this->transactionService->getTransactionsByAccount($this->account);

        $this->assertCount(2, $transactions);
    }
}
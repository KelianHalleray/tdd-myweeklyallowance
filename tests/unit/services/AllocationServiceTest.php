<?php

/**By Owen */

declare(strict_types=1);

namespace App\Tests\unit\services;

use App\entities\Account;
use App\entities\User;
use App\services\AccountService;
use App\services\TransactionService;
use App\services\AllocationService;
use PHPUnit\Framework\TestCase;

final class AllocationServiceTest extends TestCase
{
    private AllocationService $allocationService;
    private AccountService $accountService;
    private TransactionService $transactionService;
    private Account $account;

    protected function setUp(): void
    {
        $this->accountService = new AccountService();
        $this->transactionService = new TransactionService($this->accountService);
        $this->allocationService = new AllocationService(
            $this->accountService,
            $this->transactionService
        );

        $parent = new User('1', 'john@example.com', 'John');
        $this->account = $this->accountService->createAccount($parent, 'Henry', 50);
    }

    public function testSetWeeklyAllocationAttachesToAccount(): void
    {
        $allocation = $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            0
        );

        $this->assertEquals(10, $allocation->getWeeklyAmount());
        $this->assertSame($allocation, $this->account->getAllocation());
    }

    public function testSetWeeklyAllocationNegativeAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->allocationService->setWeeklyAllocation($this->account, -10, 0);
    }

    public function testSetWeeklyAllocationZeroAmountThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->allocationService->setWeeklyAllocation($this->account, 0, 0);
    }

    public function testUpdateAllocationChangesAmount(): void
    {
        $allocation = $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            0
        );

        $this->allocationService->updateAllocation($allocation, 20);

        $this->assertEquals(20, $allocation->getWeeklyAmount());
    }

    public function testUpdateAllocationNegativeAmountThrowsException(): void
    {
        $allocation = $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            0
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->allocationService->updateAllocation($allocation, -5);
    }

    public function testUpdateAllocationZeroAmountThrowsException(): void
    {
        $allocation = $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            0
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->allocationService->updateAllocation($allocation, 0);
    }

    public function testDeactivateAllocation(): void
    {
        $allocation = $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            0
        );

        $this->allocationService->deactivateAllocation($allocation);

        $this->assertFalse($allocation->isActive());
    }

    public function testProcessWeeklyAllocationsCreatesDeposits(): void
    {
        $parent = new User('1', 'kelian@example.com', 'Kelian');
        $account1 = $this->accountService->createAccount($parent, 'Kelian', 0);
        $account2 = $this->accountService->createAccount($parent, 'Owen', 0);

        $this->allocationService->setWeeklyAllocation($account1, 10, (int)date('w'));
        $this->allocationService->setWeeklyAllocation($account2, 15, (int)date('w'));

        $processed = $this->allocationService->processWeeklyAllocations([
            $account1,
            $account2
        ]);

        $this->assertCount(2, $processed);
        $this->assertEquals(10, $account1->getBalance());
        $this->assertEquals(15, $account2->getBalance());
    }

    public function testProcessWeeklyAllocationsIgnoresInactiveAllocations(): void
    {
        $parent = new User('1', 'kelian@example.com', 'Kelian');
        $account = $this->accountService->createAccount($parent, 'Owen', 0);

        $allocation = $this->allocationService->setWeeklyAllocation(
            $account,
            10,
            (int)date('w')
        );
        $this->allocationService->deactivateAllocation($allocation);

        $processed = $this->allocationService->processWeeklyAllocations([$account]);

        $this->assertEmpty($processed);
        $this->assertEquals(0, $account->getBalance());
    }

    public function testProcessWeeklyAllocationsIgnoresWrongDay(): void
    {
        $wrongDay = ((int)date('w') + 1) % 7;
        $this->allocationService->setWeeklyAllocation(
            $this->account,
            10,
            $wrongDay
        );

        $processed = $this->allocationService->processWeeklyAllocations([$this->account]);

        $this->assertEmpty($processed);
        $this->assertEquals(50, $this->account->getBalance()); 
    }

    public function testProcessWeeklyAllocationsMultipleAccounts(): void
    {
        $parent = new User('1', 'kelian@example.com', 'Kelian');
        $account1 = $this->accountService->createAccount($parent, 'Owen', 0);
        $account2 = $this->accountService->createAccount($parent, 'Kelian', 0);
        $account3 = $this->accountService->createAccount($parent, 'Stephen', 0);

        $this->allocationService->setWeeklyAllocation($account1, 10, (int)date('w'));
        $this->allocationService->setWeeklyAllocation($account2, 15, (int)date('w'));
        // account3 has no allocation

        $processed = $this->allocationService->processWeeklyAllocations([
            $account1,
            $account2,
            $account3
        ]);

        $this->assertCount(2, $processed);
        $this->assertEquals(10, $account1->getBalance());
        $this->assertEquals(15, $account2->getBalance());
        $this->assertEquals(0, $account3->getBalance());
    }
}
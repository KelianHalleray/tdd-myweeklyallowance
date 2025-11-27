<?php

/**By Owen */

declare(strict_types=1);

namespace App\services;

use App\entities\Account;
use App\entities\Allocation;

class AllocationService
{
    private AccountService $accountService;
    private TransactionService $transactionService;

    public function __construct(
        AccountService $accountService,
        TransactionService $transactionService
    ) {
        $this->accountService = $accountService;
        $this->transactionService = $transactionService;
    }

    public function setWeeklyAllocation(Account $account, float $amount, int $day): Allocation
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant doit être positif");
        }

        if ($day < 0 || $day > 6) {
            throw new \InvalidArgumentException("Jour invalide");
        }

        $allocation = new Allocation("1", $account, $amount, $day);
        $account->setAllocation($allocation);

        return $allocation;
    }

    public function updateAllocation(Allocation $allocation, float $amount): void
    {
        $allocation->setWeeklyAmount($amount);
    }

    public function deactivateAllocation(Allocation $allocation): void
    {
        $allocation->setActive(false);
    }

    public function processWeeklyAllocations(array $accounts): array
    {
        $processed = [];
        $today = (int)date("w");

        foreach ($accounts as $account) {
            $allocation = $account->getAllocation();

            if ($allocation === null) {
                continue;
            }
            if (!$allocation->isActive()) {
                continue;
            }
            if ($allocation->getDayOfWeek() !== $today) {
                continue;
            }

            $this->transactionService->deposit($account, $allocation->getWeeklyAmount());
            $processed[] = $account;
        }

        return $processed;
    }
}

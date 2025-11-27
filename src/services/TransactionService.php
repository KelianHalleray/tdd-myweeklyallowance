<?php

/**By Kelian */

declare(strict_types=1);

namespace App\services;

use App\entities\Account;
use App\entities\Transaction;

class TransactionService
{
    private AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function deposit(Account $account, float $amount, string $description = ""): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant doit être positif");
        }

        $transaction = new Transaction(
            "1",
            $account,
            Transaction::TYPE_DEPOSIT,
            $amount,
            $description
        );

        $account->addTransaction($transaction);
        $account->setBalance($account->getBalance() + $amount);

        return $transaction;
    }

    public function withdraw(Account $account, float $amount): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant doit être positif.");
        }

        if ($amount > $account->getBalance()) {
            throw new \InvalidArgumentException("Balance insuffisante.");
        }

        $transaction = new Transaction(
            "1",
            $account,
            Transaction::TYPE_EXPENSE,
            $amount
        );

        $account->addTransaction($transaction);
        $account->setBalance($account->getBalance() - $amount);

        return $transaction;
    }

    public function getTransactionsByAccount(Account $account): array
    {
        return $account->getTransactions();
    }

    public function getTotalExpenses(Account $account): float
    {
        return array_sum(array_map(
            fn(Transaction $tx) => $tx->getType() === Transaction::TYPE_EXPENSE ? $tx->getAmount() : 0,
            $account->getTransactions()
        ));
    }

    public function getTotalDeposits(Account $account): float
    {
        return array_sum(array_map(
            fn(Transaction $tx) => $tx->getType() === Transaction::TYPE_DEPOSIT ? $tx->getAmount() : 0,
            $account->getTransactions()
        ));
    }
}

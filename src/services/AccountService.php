<?php

declare(strict_types=1);

namespace App\services;

use App\entities\Account;
use App\entities\User;

class AccountService
{
    private array $accounts = [];
    private int $nextId = 1;

    public function createAccount(User $parent, string $name, float $balance = 0): Account
    {
        if ($balance < 0) {
            throw new \InvalidArgumentException("La balance ne peut être négative.");
        }

        if ($name === "") {
            throw new \InvalidArgumentException("Le nom ne peut être vide.");
        }

        $id = (string)$this->nextId++;
        $account = new Account($id, $name, $parent);
        $account->setBalance($balance);

        $this->accounts[$id] = $account;

        return $account;
    }

    public function updateBalance(Account $account, float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("La balance ne peut être négative.");
        }
        $account->setBalance($amount);
    }

    public function getAccountsByParent(User $parent): array
    {
        return array_filter(
            $this->accounts,
            fn(Account $acc) => $acc->getParent()->getId() === $parent->getId()
        );
    }
}
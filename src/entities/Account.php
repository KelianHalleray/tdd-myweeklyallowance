<?php

declare(strict_types=1);

namespace App\entities;

class Account
{
    private string $id;
    private string $adolescentName;
    private User $parent;
    private float $balance = 0;
    private float $weeklyAllocation = 0;

    private array $transactions = [];

    private ?Allocation $allocation = null;

    public function __construct(string $id, string $adolescentName, User $parent)
    {
        $this->id = $id;
        $this->adolescentName = $adolescentName;
        $this->parent = $parent;
    }

    public function getId(): string { return $this->id; }

    public function getAdolescentName(): string { return $this->adolescentName; }

    public function getParent(): User { return $this->parent; }

    public function getBalance(): float { return $this->balance; }

    public function setBalance(float $amount): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException("La balance ne peut être négative.");
        }
        $this->balance = $amount;
    }

    public function getWeeklyAllocation(): float
    {
        return $this->weeklyAllocation;
    }

    public function setWeeklyAllocation(float $amount): void
    {
        $this->weeklyAllocation = $amount;
    }

    public function addTransaction(Transaction $transaction): void
    {
        $this->transactions[] = $transaction;
    }

    public function getTransactions(): array
    {
        return $this->transactions;
    }

    public function getAllocation(): ?Allocation
    {
        return $this->allocation;
    }

    public function setAllocation(?Allocation $allocation): void
    {
        $this->allocation = $allocation;
    }
}

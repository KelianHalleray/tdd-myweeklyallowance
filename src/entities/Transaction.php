<?php

declare(strict_types=1);

namespace App\entities;

class Transaction
{
    public const TYPE_DEPOSIT = 'deposit';
    public const TYPE_EXPENSE = 'expense';

    private string $id;
    private Account $account;
    private string $type;
    private float $amount;
    private string $description;
    private \DateTime $date;

    public function __construct(
        string $id,
        Account $account,
        string $type,
        float $amount,
        string $description = "",
        ?\DateTime $date = null
    ) {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Le montant doit être positif.");
        }

        if (!in_array($type, [self::TYPE_DEPOSIT, self::TYPE_EXPENSE], true)) {
            throw new \InvalidArgumentException("Mauvais type de transaction.");
        }

        $this->id = $id;
        $this->account = $account;
        $this->type = $type;
        $this->amount = $amount;
        $this->description = $description ?? "";
        $this->date = $date ?? new \DateTime();
    }

    public function getId(): string { return $this->id; }
    public function getAccount(): Account { return $this->account; }
    public function getType(): string { return $this->type; }
    public function getAmount(): float { return $this->amount; }
    public function getDescription(): string { return $this->description; }
    public function getDate(): \DateTime { return $this->date; }
}

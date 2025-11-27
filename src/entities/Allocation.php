<?php

/**By Owen */

declare(strict_types=1);

namespace App\entities;

class Allocation
{
    private string $id;
    private Account $account;
    private float $weeklyAmount;
    private int $dayOfWeek;
    private bool $active = true;

    public function __construct(
        string $id,
        Account $account,
        float $weeklyAmount,
        int $dayOfWeek
    ) {
        if ($weeklyAmount <= 0) {
            throw new \InvalidArgumentException("La valeur doit être positive");
        }

        if ($dayOfWeek < 0 || $dayOfWeek > 6) {
            throw new \InvalidArgumentException("Le jour de la semaine doit être dans la plage : 0 -> 6");
        }

        $this->id = $id;
        $this->account = $account;
        $this->weeklyAmount = $weeklyAmount;
        $this->dayOfWeek = $dayOfWeek;
    }

    public function getId(): string { return $this->id; }

    public function getAccount(): Account { return $this->account; }

    public function getWeeklyAmount(): float { return $this->weeklyAmount; }

    public function setWeeklyAmount(float $amount): void
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException("Merci de rentrer une valeur positive");
        }
        $this->weeklyAmount = $amount;
    }

    public function getDayOfWeek(): int { return $this->dayOfWeek; }

    public function isActive(): bool { return $this->active; }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }
}

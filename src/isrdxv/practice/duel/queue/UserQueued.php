<?php
declare(strict_types=1);

namespace isrdxv\practice\duel\queue;

final class UserQueued
{
    private string $name;

    private string $kit;

    private bool $ranked;

    function __construct(string $name, string $kit, bool $ranked = false)
    {
        $this->name = $name;
        $this->kit = $kit;
        $this->ranked = $ranked;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getKit(): string
    {
        return $this->kit;
    }

    function isRanked(): bool
    {
        return $this->ranked;
    }
}
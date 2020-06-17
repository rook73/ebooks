<?php

namespace App\Entity;

use DateTimeInterface;

interface CreateDateTimeInterface
{
    public function getCreatedAt(): ?DateTimeInterface;

    public function setCreatedAt(DateTimeInterface $value);
}

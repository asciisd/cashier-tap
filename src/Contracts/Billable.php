<?php

namespace Asciisd\Cashier\Contracts;

interface Billable
{
    public function tapOptions(array $options = []): array;
    public function tapCustomerFields(): array;

    public function tapId(): ?string;
    public function tapEmail(): string;
    public function tapPhone(): array;
    public function tapFirstName(): string;
    public function tapLastName(): string;
}
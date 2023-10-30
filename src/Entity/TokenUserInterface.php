<?php

namespace AccessToken\Entity;

interface TokenUserInterface
{
    public function getPublicId(): string;

    public function isVerified(): ?bool;

    public function isActive(): ?bool;

    public function getUserIdentifierValue(): string;
}
<?php

namespace Test;

class TestUserWithConstructor
{
    public function __construct(
        public readonly string $id,
        public string $name,
        public string $email
    ) {}

    public function setEmail(string $email): void {
        $this->email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

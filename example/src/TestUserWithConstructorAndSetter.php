<?php

namespace Test;

class TestUserWithConstructorAndSetter
{
    private string $email;

    public function __construct(
        public readonly string $id,
        public string $name
    ) {}

    public function setEmail(string $email): void {
        $this->email = filter_var($email, FILTER_VALIDATE_EMAIL);
    }
}

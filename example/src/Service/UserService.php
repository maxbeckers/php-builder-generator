<?php

namespace Test\Service;

use Test\ReferencedObject\Email;
use Test\UserWithReference;
use Test\UserWithReferenceBuilder;

class UserService
{
    public static function createUser(): UserWithReference
    {
        return UserWithReferenceBuilder::builder()
            ->name('John Doe')
            ->age(30)
            ->roles(['admin', 'user'])
            ->active(true)
            ->email(new Email('test@mail.com'))
            ->build();
    }
}

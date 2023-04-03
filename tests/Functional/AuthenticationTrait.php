<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;

trait AuthenticationTrait
{
    public static function getAuthUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('test@test.test');

        return $user;
    }
}

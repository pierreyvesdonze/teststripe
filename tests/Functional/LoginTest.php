<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginTest extends WebTestCase
{

    use AuthenticationTrait;

    //php bin/phpunit --filter testLoginSuccess .
    public function testLoginSuccess()
    {
        $client = static::createClient();

        $user = static::getAuthUser();

        $client->loginUser($user);
        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('main');
    }

    //php bin/phpunit --filter testLoginFail .
    public function testLoginFail()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $testUser = $userRepository->findOneByEmail("pouet");

        $client->loginUser($testUser);
        $client->request('GET', '/login');

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertSelectorTextContains('html', "Email ou mot de passe invalide");
    }
}

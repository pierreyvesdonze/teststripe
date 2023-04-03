<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrationTest extends WebTestCase
{
    //php bin/phpunit --filter testRegistrationSuccess .
    public function testRegistrationSuccess(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->filter("form[name=registration_form]")->form([
            "registration_form[lastname]" => "Doe",
            "registration_form[firstname]" => "John",
            "registration_form[email]" => "jd@jd.jd",
            "registration_form[plainPassword][first]" => "testtest",
            "registration_form[plainPassword][second]" => "testtest",
            "registration_form[phoneNumber]" => "0606060606",
            "registration_form[addressFirstLine]" => "2 rue de la rue",
            "registration_form[addressSecondLine]" => "Près du pré aussi",
            "registration_form[addressPostal]" => "75000",
            "registration_form[town]" => "Paris",
        ]);

        $client->submit($form);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        $this->assertRouteSame('login');
    }
}

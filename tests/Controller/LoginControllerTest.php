<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\UrlHelper;

class LoginControllerTest extends WebTestCase
{
    public function testLoginFormIsDisplayedCorrectly(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('label[for="username"]', 'Email');
        $this->assertSelectorTextContains('label[for="password"]', 'Password');
        $this->assertSelectorTextContains('button', 'Login');
    }

    public function testLoginRedirectsToFrontpage(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            '_username' => 'admin@example.com',
            '_password' => '12345678',
        ]);

        $urlHelper = static::getContainer()->get(UrlHelper::class);

        $this->assertResponseRedirects($urlHelper->getAbsoluteUrl('/'), 302);
    }

    public function testIncorrectPasswordRedirectsToLoginForm(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            '_username' => 'admin@example.com',
            '_password' => 'INCORRECT',
        ]);

        $urlHelper = static::getContainer()->get(UrlHelper::class);

        $this->assertResponseRedirects($urlHelper->getAbsoluteUrl('/login'), 302);

        $client->followRedirect();

        $this->assertSelectorTextContains('div', 'Invalid credentials');
    }

    public function testIncorrectUsernameRedirectsToLoginForm(): void
    {
        $client = static::createClient();
        $client->request('POST', '/login', [
            '_username' => 'wrong@example.com',
            '_password' => '12345678',
        ]);

        $urlHelper = static::getContainer()->get(UrlHelper::class);

        $this->assertResponseRedirects($urlHelper->getAbsoluteUrl('/login'), 302);

        $client->followRedirect();

        $this->assertSelectorTextContains('div', 'Invalid credentials');
    }
}

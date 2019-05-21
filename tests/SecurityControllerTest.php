<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures​;
use App\Tests\AbstractTest;
use App\Tests\Mock\BillingClientMock;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SecurityControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures​::class];
    }

    public function authClient($email, $password)
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = $email;
        $form["password"] = $password;
        $client->submit($form);
        return $client;
    }

    public function regClient($email, $password, $repeatPassword)
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["registration[email]"] = $email;
        $form["registration[password]"] = $password;
        $form["registration[repeatPassword]"] = $repeatPassword;
        $client->submit($form);
        return $client;
    }

    public function fillRegForm($client, $email, $password, $repeatPassword)
    {
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["registration[email]"] = $email;
        $form["registration[password]"] = $password;
        $form["registration[repeatPassword]"] = $repeatPassword;
        return $form;
    }

    /**
     * Login Tests
     */
    public function testAdminLogin()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->followRedirect();
        $crawler = $client->clickLink('adminUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Администратор")')->count() > 0);
    }

    public function testUserLogin()
    {
        $client = $this->authClient('simpleUser@gmail.com', 'passwordForSimpleUser');
        $client->followRedirect();
        $crawler = $client->clickLink('simpleUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Пользователь")')->count() > 0);
    }

    public function testLoginWrongEmail()
    {
        $client = $this->authClient('user@gmail.com', 'passwordForSimpleUser');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Bad credentials, please verify your username and password")')->count() > 0);
    }

    public function testLoginWrongPassword()
    {
        $client = $this->authClient('simpleUser@gmail.com', 'passwordForSimpleUser123');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Bad credentials, please verify your username and password")')->count() > 0);
    }

    public function testUserLogout()
    {
        $client = $this->authClient('simpleUser@gmail.com', 'passwordForSimpleUser');
        $crawler = $client->followRedirect();
        $crawler = $client->clickLink('simpleUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Пользователь")')->count() > 0);
        $client->request('GET', '/courses/');
        $client->clickLink('Выход');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Вход")')->count() > 0);
    }

    public function testLoginWithBillingError()
    {
        $client = $this->authClient('throwException@mail.ru', 'passwordForSimpleUser');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Сервис временно недоступен. Попробуйте авторизоваться позднее")')->count() > 0);
    }

    /**
     * Register Tests
     */
    public function testRegisterNewUser()
    {
        $client = $this->regClient('user@gmail.com', '1234567', '1234567');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("user@gmail.com")')->count() > 0);
    }

    public function testRegisterExistingEmail()
    {
        $client = static::createClient();
        $crawler = $client->submit($this->fillRegForm($client, 'simpleUser@gmail.com', '1234567', '1234567'));
        $this->assertTrue($crawler->filter('html:contains("Email already exists")')->count() > 0);
    }

    public function testRegisterShortPassword()
    {
        $client = static::createClient();
        $crawler = $client->submit($this->fillRegForm($client, 'simpleUser@gmail.com', '123', '123'));
        $this->assertTrue($crawler->filter('html:contains("Your password should be at least 6 symbols")')->count() > 0);
    }

    public function testRegisterDiffPasswords()
    {
        $client = static::createClient();
        $crawler = $client->submit($this->fillRegForm($client, 'simpleUser@gmail.com', '1234567', '7654321'));
        $this->assertTrue($crawler->filter('html:contains("Passwords must be the same")')->count() > 0);
    }

    public function testRegisterInvalidEmail()
    {
        $client = static::createClient();
        $crawler = $client->submit($this->fillRegForm($client, 'usergmail.com', '1234567', '1234567'));
        $this->assertTrue($crawler->filter('html:contains("Invalid email")')->count() > 0);
    }

    public function testRegisterAfterLogin()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->followRedirect();
        $client->request('GET', '/register');
        $this->assertTrue($client->getResponse()->isRedirect('/profile'));
    }

    public function testAnonymousProfileShow()
    {
        $client = static::createClient();
        $client->request('GET', '/profile');
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testRegWithBillingError()
    {
        $client = static::createClient();
        $crawler = $client->submit($this->fillRegForm($client, 'throwException@mail.ru', '1234567', '1234567'));
        $this->assertTrue($crawler->filter('html:contains("Сервис временно недоступен. Попробуйте зарегистрироваться позднее")')->count() > 0);
    }
}

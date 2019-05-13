<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures​;
use App\Tests\AbstractTest;
use App\Tests\Mock\BillingClientMock;

class SecurityControllerTest extends AbstractTest
{
    public function getFixtures(): array
    {
        return [CourseFixtures​::class];
    }

    /**
     * Login Tests
     */
    public function testAdminLogin()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'adminUser@gmail.com';
        $form["password"] = 'passwordForAdminUser';
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->clickLink('adminUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Администратор")')->count() > 0);
    }

    public function testUserLogin()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'simpleUser@gmail.com';
        $form["password"] = 'passwordForSimpleUser';
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->clickLink('simpleUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Пользователь")')->count() > 0);
    }

    public function testLoginWrongEmail()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'user@gmail.com';
        $form["password"] = 'passwordForSimpleUser';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Bad credentials, please verify your username and password")')->count() > 0);
    }

    public function testLoginWrongPassword()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'simpleUser@gmail.com';
        $form["password"] = 'passwordForSimpleUser123';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Bad credentials, please verify your username and password")')->count() > 0);
    }

    public function testUserLogout()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'simpleUser@gmail.com';
        $form["password"] = 'passwordForSimpleUser';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $crawler = $client->clickLink('simpleUser@gmail.com');
        $this->assertTrue($crawler->filter('html:contains("Роль: Пользователь")')->count() > 0);
        $client->request('GET', '/courses/');
        $client->clickLink('Выход');
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Вход")')->count() > 0);
    }

    /**
     * Register Tests
     */
    public function testRegisterNewUser()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["form[email]"] = 'user@gmail.com';
        $form["form[password]"] = '1234567';
        $form["form[repeatPassword]"] = '1234567';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("user@gmail.com")')->count() > 0);
    }

    public function testRegisterExistingEmail()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["form[email]"] = 'simpleUser@gmail.com';
        $form["form[password]"] = '1234567';
        $form["form[repeatPassword]"] = '1234567';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("Email already exists")')->count() > 0);
    }

    public function testRegisterShortPassword()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["form[email]"] = 'user@gmail.com';
        $form["form[password]"] = '123';
        $form["form[repeatPassword]"] = '123';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("Your password should be at least 6 symbols")')->count() > 0);
    }

    public function testRegisterDiffPasswords()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["form[email]"] = 'user@gmail.com';
        $form["form[password]"] = '1234567';
        $form["form[repeatPassword]"] = '7654321';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("Passwords must be the same")')->count() > 0);
    }

    public function testRegisterInvalidEmail()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $client->clickLink('Вход');
        $crawler = $client->clickLink('Зарегистрироваться');
        $form = $crawler->selectButton('Зарегистрироваться')->form();
        $form["form[email]"] = 'usergmail.com';
        $form["form[password]"] = '1234567';
        $form["form[repeatPassword]"] = '1234567';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("Invalid email")')->count() > 0);
    }

    public function testRegisterAfterLogin()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'adminUser@gmail.com';
        $form["password"] = 'passwordForAdminUser';
        $client->submit($form);
        $client->followRedirect();
        $client->request('GET', '/register');
        $this->assertTrue($client->getResponse()->isRedirect('/profile'));
    }

    /**
     * Access Control Tests. Anonymous User
     */
    public function testAnonymousAddCourse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/new');
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAnonymousAddLesson()
    {
        $client = static::createClient();
        $client->request('GET', '/lessons/new');
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAnonymousLessonShow()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Пройти курс');
        $link = $crawler->filter('.lessonShow')->first();
        $client->clickLink($link->text());
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testAnonymousProfileShow()
    {
        $client = static::createClient();
        $client->request('GET', '/profile');
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    /**
     * Access Control Tests. Logged In User
     */
    public function testLoggedInUserAddCourse()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'simpleUser@gmail.com';
        $form["password"] = 'passwordForSimpleUser';
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->request('GET', '/courses/new');
        $this->assertTrue($crawler->filter('html:contains("Доступ запрещен!")')->count() > 0);
    }

    public function testLoggedInUserAddLesson()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $crawler = $client->clickLink('Вход');
        $form = $crawler->selectButton('Войти')->form();
        $form["email"] = 'simpleUser@gmail.com';
        $form["password"] = 'passwordForSimpleUser';
        $client->submit($form);
        $client->followRedirect();
        $crawler = $client->request('GET', '/lessons/new');
        $this->assertTrue($crawler->filter('html:contains("Доступ запрещен!")')->count() > 0);
    }
}

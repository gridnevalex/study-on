<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\DataFixtures\CourseFixtures​;
use App\Tests\AbstractTest;
use App\Tests\Mock\BillingClientMock;

class CourseControllerTest extends AbstractTest
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
        $client->followRedirect();
        return $client;
    }

    public function testIndexResponse()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testNewResponse()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->clickLink('Новый курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowResponse()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $client->clickLink('Пройти курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditResponse()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->clickLink('Пройти курс');
        $client->clickLink('Редактировать курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCountCourses()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $crawler = $client->request('GET', '/courses/');
        $this->assertEquals(4, $crawler->filter('.card-title')->count());
    }

    public function testCourse404()
    {
        $client = static::createClient();
        $client->disableReboot();
        $client->getContainer()->set('App\Service\BillingClient', new BillingClientMock($_ENV['BILLING_HOST']));
        $client->request('GET', '/courses/mern-stack');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCourseEdit404()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->request('GET', '/courses/25/edit');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCourseAdd()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'new course';
        $form["course[description]"] = 'Описание нового курса';
        $form["course[type]"] = 'buy';
        $form["course[price]"] = '50.5';
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(5, $crawler->filter('.card-title')->count());
    }

    public function testCourseEdit()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $client->clickLink('Пройти курс');
        $crawler = $client->clickLink('Редактировать курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'new-course!';
        $form["course[description]"] = 'Описание нового курса!!!';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Описание нового курса!!!")')->count() > 0);
    }

    public function testCourseDelete()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $crawler = $client->clickLink('Пройти курс');
        $form = $crawler->selectButton('Удалить')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(3, $crawler->filter('.card-title')->count());
    }

    public function testCourseDeleteWithLessons()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $crawler = $client->request('GET', '/courses/');
        $link = $crawler->filter('a')->eq(4);
        $client->clickLink($link->text());
        $crawler = $client->clickLink('Добавить урок');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["lesson[name]"] = 'Новый урок';
        $form["lesson[content]"] = 'Описание нового урока';
        $form["lesson[serialNumber]"] = 10;
        $client->submit($form);
        $crawler = $client->followRedirect();
        $form = $crawler->selectButton('Удалить')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(3, $crawler->filter('.card-title')->count());
    }

    public function testCourseAddWithBlankName()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = '';
        $form["course[description]"] = 'Описание нового курса';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("This value must no be empty")')->count() > 0);
    }

    public function testCourseAddWithBlankDescriprion()
    {
        $client = $this->authClient('adminUser@gmail.com', 'passwordForAdminUser');
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'Новый курс';
        $form["course[description]"] = '';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("This value must no be empty")')->count() > 0);
    }

    public function testAnonymousAddCourse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/new');
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function testLoggedInUserAddCourse()
    {
        $client = $this->authClient('simpleUser@gmail.com', 'passwordForSimpleUser');
        $crawler = $client->request('GET', '/courses/new');
        $this->assertTrue($crawler->filter('html:contains("Доступ запрещен!")')->count() > 0);
    }
}

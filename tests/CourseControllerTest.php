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

    public function testIndexResponse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function testNewResponse()
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
        $client->clickLink('Новый курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testShowResponse()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/');
        $client->clickLink('Пройти курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testEditResponse()
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
        $client->clickLink('Пройти курс');
        $client->clickLink('Редактировать курс');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCountCourses()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertEquals(3, $crawler->filter('.card-title')->count());
    }

    public function testCourse404()
    {
        $client = static::createClient();
        $client->request('GET', '/courses/25');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCourseEdit404()
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
        $client->request('GET', '/courses/25/edit');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function testCourseAdd()
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
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'Новый курс';
        $form["course[description]"] = 'Описание нового курса';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(4, $crawler->filter('.card-title')->count());
    }

    public function testCourseEdit()
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
        $client->clickLink('Пройти курс');
        $crawler = $client->clickLink('Редактировать курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'Новый курc';
        $form["course[description]"] = 'Описание нового курса!!!';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertTrue($crawler->filter('html:contains("Описание нового курса!!!")')->count() > 0);
    }

    public function testCourseDelete()
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
        $crawler = $client->clickLink('Пройти курс');
        $form = $crawler->selectButton('Удалить')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(2, $crawler->filter('.card-title')->count());
    }

    public function testCourseDeleteWithLessons()
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
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'Новый курс';
        $form["course[description]"] = 'Описание нового курса';
        $client->submit($form);
        $crawler = $client->followRedirect();
        $link = $crawler->filter('a')->last();
        $client->clickLink($link->text());
        $crawler = $client->clickLink('Добавить урок');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["lesson[name]"] = 'Новый урок';
        $form["lesson[content]"] = 'Описание нового урока';
        $form["lesson[serialNumber]"] = 10;
        $crawler = $client->submit($form);
        $form = $crawler->selectButton('Удалить')->form();
        $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals(3, $crawler->filter('.card-title')->count());
    }

    public function testCourseAddWithBlankName()
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
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = '';
        $form["course[description]"] = 'Описание нового курса';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("This value should not be blank")')->count() > 0);
    }

    public function testCourseAddWithBlankDescriprion()
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
        $crawler = $client->clickLink('Новый курс');
        $form = $crawler->selectButton('Сохранить')->form();
        $form["course[name]"] = 'Новый курс';
        $form["course[description]"] = '';
        $crawler = $client->submit($form);
        $this->assertTrue($crawler->filter('html:contains("This value should not be blank")')->count() > 0);
    }
}

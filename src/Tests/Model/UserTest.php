<?php

namespace Weather\Tests\Model;

use TestTools\TestCase\UnitTestCase;

class UserTest extends UnitTestCase
{
    /**
     * @var \Weather\Model\UserModel
     */
    protected $model;

    public function setUp()
    {
        $this->model = $this->get('model.user');
    }

    public function testFind()
    {
        $this->model->find(1);

        $this->assertEquals(1, $this->model->getId());
        $this->assertEquals(1, $this->model->admin);
    }

    public function testFindByCredentials() {
        $result = $this->model->findByCredentials('admin@example.com', 'passwd');

        $this->assertInstanceOf('\Weather\Model\UserModel', $result);

        $this->assertEquals(1, $result->getId());
        $this->assertEquals(1, $result->admin);
        $this->assertEquals('Admin', $result->firstname);
        $this->assertEquals('Silex', $result->lastname);
        $this->assertEquals('admin@example.com', $result->email);
    }

    /**
     * @expectedException \Weather\Exception\NotFoundException
     */
    public function testFindByCredentialsNotFound() {
        $this->model->findByCredentials('admin2@example.com', 'passwd');
    }

    /**
     * @expectedException \Weather\Exception\InvalidPasswordException
     */
    public function testFindByCredentialsInvalidPassword() {
        $this->model->findByCredentials('admin@example.com', 'passwd2');
    }

    /**
     * @expectedException \Doctrine\ActiveRecord\Exception\Exception
     */
    public function testGetPasswordException() {
        $this->model->password;
    }

    /**
     * @expectedException \Weather\Exception\InvalidArgumentException
     */
    public function testInsecurePassword() {
        $password = 'fooBar';

        $this->model->find(2);

        $this->model->updatePassword($password);
    }

    public function testAdminUser()
    {
        $user = $this->model->findByCredentials('admin@example.com', 'passwd');

        $expected = '$6$5ygXjBO2gNbW$p1eaS7isBLD1JfN6PaQzrGKJHf9UGmUOBCZiqq3VnhDSPhdbIzOnu3kbKO2mcKEFiD11jFoPE5YSyvA7cYbbK1';
        $this->assertEquals($expected, $user->password);
    }
}
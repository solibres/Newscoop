<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Newscoop\Entity\User;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var Newscoop\Services\UserService */
    protected $service;

    /** @var Zend_Auth */
    protected $auth;

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /** @var Newscoop\Entity\Repository\UserRepository */
    protected $repository;

    public function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->auth = $this->getMockBuilder('Zend_Auth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Newscoop\Entity\Repository\UserRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->service = new UserService($this->em, $this->auth);
    }

    public function testUser()
    {
        $service = new UserService($this->em, $this->auth);
        $this->assertInstanceOf('Newscoop\Services\UserService', $service);
    }

    public function testGetCurrentUser()
    {
        $user = $this->getMock('Newscoop\Entity\User');

        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(true));

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(1));

        $this->expectGetRepository();

        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $this->assertEquals($user, $this->service->getCurrentUser());
        $this->assertEquals($user, $this->service->getCurrentUser()); // test if getting user only once
    }

    public function testGetCurrentUserNotAuthorized()
    {
        $this->auth->expects($this->once())
            ->method('hasIdentity')
            ->will($this->returnValue(false));

        $this->assertNull($this->service->getCurrentUser());
    }

    public function testFind()
    {
        $user = new User();
        $this->expectGetRepository();
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $this->assertEquals($user, $this->service->find(1));
    }

    public function testSaveNew()
    {
        $this->expectGetRepository();

        $userdata = array(
            'username' => 'foobar',
            'first_name' => 'foo',
            'last_name' => 'bar',
            'email' => 'foo@bar.com',
        );

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf('Newscoop\Entity\User'), $this->equalTo($userdata));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->assertInstanceOf('Newscoop\Entity\User', $this->service->save($userdata));
    }

    public function testDelete()
    {
        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(3));

        $user = $this->getMock('Newscoop\Entity\User');
        $user->expects($this->once())
            ->method('setStatus')
            ->with($this->equalTo(User::STATUS_DELETED));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->service->delete($user);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testDeleteHimself()
    {
        $user = new User();
        $property = new \ReflectionProperty($user, 'id');
        $property->setAccessible(TRUE);
        $property->setValue($user, 1);

        $this->auth->expects($this->once())
            ->method('getIdentity')
            ->will($this->returnValue(1));

        $this->service->delete($user);
    }

    public function testGenerateUsername()
    {
        $user = new User();
        $user->setUsername('foo.bar');

        $this->em->expects($this->atLeastOnce())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));

        $this->repository->expects($this->any())
            ->method('findOneBy')
            ->will($this->onConsecutiveCalls(null, $user, null));

        $this->assertEquals('foo.bar', $this->service->generateUsername('Foo', 'Bar'));
        $this->assertEquals('foo.bar1', $this->service->generateUsername('Foo', 'Bar'));
        $this->assertEquals('', $this->service->generateUsername('', ''));
        $this->assertEquals('foo', $this->service->generateUsername('Foo', ''));
        $this->assertEquals('bar', $this->service->generateUsername('', 'Bar'));
    }

    public function testSetActive()
    {
        $user = new User();
        $user->setUsername('foo');
        $this->assertFalse($user->isActive());

        $this->em->expects($this->once())
            ->method('flush')
            ->with();

        $this->service->setActive($user);
        $this->assertTrue($user->isActive());
    }

    public function testSave()
    {
        $user = new User('foo');
        $data = array(
            'email' => 'info@example.com',
        );

        $this->expectGetRepository();

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($user), $this->equalTo($data));

        $this->em->expects($this->once())
            ->method('flush');

        $this->assertEquals($user, $this->service->save($data, $user));
    }

    public function testCreatePending()
    {
        $this->em->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf('Newscoop\Entity\User'));

        $this->em->expects($this->once())
            ->method('flush')
            ->with();
            
        $user = $this->service->createPending('email@example.com');
        $this->assertInstanceOf('Newscoop\Entity\User', $user);
    }

    public function testSavePending()
    {
        $data = array();
        $user = new User('email');

        $this->expectGetRepository();

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->equalTo($user), $this->equalTo($data));

        $this->service->savePending($data, $user);
        $this->assertTrue($user->isActive());
    }

    protected function expectGetRepository()
    {
        $this->em->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('Newscoop\Entity\User'))
            ->will($this->returnValue($this->repository));
    }
}

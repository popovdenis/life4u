<?php
/**
 * @author Sergey Palamarchuk
 * @email pase@ciklum.com
 */

use LifeSafe4U\UserBundle\Entity\User;

class UserTest extends \LifeSafe4U\BasicBundle\Tests\TestCase
{
    /**
     * @var \LifeSafe4U\UserBundle\Entity\User
     */
    protected $entity;

    public function setUp()
    {
        parent::setUp();

        $this->entity = new User();
    }

    public function testEmail()
    {
        $this->checkField(__FUNCTION__, 'test@email.com');
    }

    public function testPassword()
    {
        $this->checkField(__FUNCTION__, 'password');
    }

    public function testPlainPassword()
    {
        $this->checkField(__FUNCTION__, 'password');
    }

    public function testSalt()
    {
        $this->checkField(__FUNCTION__, 'salt');
    }

    public function testRoles()
    {
        $role = User::ROLE_USER;

        $this->entity->setRoles(array($role));
        $this->assertContains($role, $this->entity->getRoles());
    }

    /*
     * @depends testRoles
     */
    public function testAddRemoveRole()
    {
        $role = 'ROLE_TEST';

        $this->entity->addRole($role);
        $this->assertContains($role, $this->entity->getRoles());
        $this->entity->removeRole($role);
        $this->assertNotContains($role, $this->entity->getRoles());
    }

    public function testEmailConfirmed()
    {
        $this->checkField(__FUNCTION__, true, false);
    }

    /*
     * @depends testEmailConfirmed
     */
    public function testConfirmEmail()
    {
        $this->entity->setEmailConfirmed(false);

        $this->entity->confirmEmail();
        $this->assertTrue($this->entity->getEmailConfirmed());
    }

    public function testFirstName()
    {
        $this->checkField(__FUNCTION__, 'First');
    }

    public function testLastName()
    {
        $this->checkField(__FUNCTION__, 'Last');
    }

    public function testPhone()
    {
        $this->checkField(__FUNCTION__, '+3801111111');
    }

    public function testPicture()
    {
        $this->checkField(__FUNCTION__, 'picture.png');
    }

    public function testLocale()
    {
        $this->checkField(__FUNCTION__, 'en');
    }

    public function testEnabled()
    {
        $this->entity->setEnabled(true);
        $this->assertTrue($this->entity->getEnabled());
    }

    public function testIsEnabled()
    {
        $this->entity->setEnabled(true);
        $this->assertTrue($this->entity->isEnabled());
    }

    /**
     * @depends testEnabled
     */
    public function testEnable()
    {
        $this->entity->setEnabled(false);
        $this->entity->enable();
        $this->assertTrue($this->entity->getEnabled());
    }

    /**
     * @depends testEnabled
     */
    public function testDisable()
    {
        $this->entity->setEnabled(true);
        $this->entity->disable();
        $this->assertFalse($this->entity->getEnabled());
    }

    public function testCreatedAt()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }

    public function testUpdatedAt()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }

    public function testDeletedAt()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }

    /**
     * @depends testDeletedAt
     */
    public function testDelete()
    {
        $this->entity->delete();
        $this->assertNotNull($this->entity->getDeletedAt());
    }

    public function testLastLogin()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }

    public function testPasswordRequestedAt()
    {
        $this->checkField(__FUNCTION__, new \DateTime());
    }

    public function testIsPasswordRequestNonExpired()
    {
        $ttl = 1;
        $this->entity->setPasswordRequestedAt(new \DateTime());
        $this->assertTrue($this->entity->isPasswordRequestNonExpired($ttl));
        $this->entity->setPasswordRequestedAt(new \DateTime('yesterday'));
        $this->assertFalse($this->entity->isPasswordRequestNonExpired($ttl));
    }

    public function testConfirmationToken()
    {
        $this->checkField(__FUNCTION__, 'token');
    }

    /**
     * @depends testEmail
     * @depends testPassword
     * @depends testSalt
     * @depends testEnabled
     */
    public function testIsEqualTo()
    {
        $user = new User();
        $user
            ->setEmail('notequal@email.com')
            ->setPassword('notequalpassword')
            ->setSalt('notequalsalt')
            ->setEnabled(!$this->entity->isEnabled());
        $this->assertFalse($this->entity->isEqualTo($user));

        $user->setPassword($this->entity->getPassword());
        $this->assertFalse($this->entity->isEqualTo($user));
        $user->setEmail($this->entity->getEmail());
        $this->assertFalse($this->entity->isEqualTo($user));
        $user->setSalt($this->entity->getSalt());
        $this->assertFalse($this->entity->isEqualTo($user));
        $user->setEnabled($this->entity->getEnabled());
        $this->assertTrue($this->entity->isEqualTo($user));
    }

    /**
     * @depends testPlainPassword
     */
    public function testEraseCredentials()
    {
        $this->entity->setPlainPassword('password');
        $this->entity->eraseCredentials();
        $this->assertNull($this->entity->getPlainPassword());
    }

    /**
     * @depends testConfirmationToken
     */
    public function testGenerateConfirmationToken()
    {
        $this->entity->generateConfirmationToken();
        $this->assertNotNull($this->entity->getConfirmationToken());
    }
}

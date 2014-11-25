<?php
/**
 * @author Sergey Palamarchuk
 * @email pase@ciklum.com
 */

namespace LifeSafe4U\UserBundle\Model;

use Doctrine\Common\Persistence\ObjectManager;
useLifeSafe4Ue\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserManager implements UserProviderInterface
{
    /**
     * @var \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface
     */
    protected $encoderFactory;
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $entityManager;
    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param ObjectManager $em
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(ObjectManager $em, EncoderFactoryInterface $encoderFactory)
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $em;
        $this->repository = $em->getRepository($this->getClass());
    }

    /**
     * @return string
     */
    public function getClass()
    {
        returnLifeSafe4Ure\UserBundle\Entity\User';
    }

    /**
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param integer $id
     * @return User|null
     */
    public function findUser($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @return mixed
     */
    public function findUsers()
    {
        return $this->getRepository()->findAll();
    }

    /**
     * @param array $criteria
     * @return mixed
     */
    public function findUsersBy(array $criteria)
    {
        return $this->getRepository()->findBy($criteria);
    }

    /**
     * Finds a user by email
     *
     * @param string $email
     * @return User
     */
    public function findUserByEmail($email)
    {
        return $this->findUserBy(array('email' => $email));
    }

    /**
     * @param array $criteria
     * @return User|null
     */
    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    /**
     * Loads a user by username
     *
     * It is strongly discouraged to call this method manually as it bypasses
     * all ACL checks.
     *
     * @param string $username
     * @throws UsernameNotFoundException
     * @return User
     */
    public function loadUserByUsername($username)
    {
        $user = $this->findUserByEmail($username);

        if (!$user instanceof AdvancedUserInterface) {
            throw new UsernameNotFoundException(sprintf('No user with email "%s" was found.', $username));
        }
        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $refreshedUser = $this->findUserBy(array('id' => $user->getId()));
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $refreshedUser;
    }

    /**
     * Returns an empty user instance
     *
     * @return User
     */
    public function create()
    {
        $class = $this->getClass();
        $user = new $class;

        return $user;
    }

    /**
     * Updates a user.
     *
     * @param User $user
     * @param boolean $andFlush Whether to flush the changes (default true)
     * @param boolean $updatePassword Whether to update password (default true)
     */
    public function update(User $user, $andFlush = true, $updatePassword = true)
    {

        if ($updatePassword) {
            $this->updatePassword($user);
        }

        $this->entityManager->persist($user);
        if ($andFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * @param User $user
     */
    public function updatePassword(User $user)
    {
        if (0 !== strlen($password = $user->getPlainPassword())) {
            $encoder = $this->getEncoder($user);
            $user->setPassword($encoder->encodePassword($password, $user->getSalt()));
            $user->eraseCredentials();
        }
    }

    /**
     * Deletes a user.
     *
     * @param UserInterface $user
     * @param boolean $andFlush Whether to flush the changes (default true)
     * @retrun void
     */
    public function delete(User $user, $andFlush = true)
    {
        $user->disable();
        $user->removeUpload();
        $user->setEmailOld($user->getEmail());
        $user->setEmail(null);
        $user->setEmailConfirmed(false);
        $this->entityManager->remove($user);
        if ($andFlush) {
            $this->entityManager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->getClass();
    }

    /**
     * @param User $user
     * @return \Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface
     */
    protected function getEncoder(User $user)
    {
        return $this->encoderFactory->getEncoder($user);
    }
}

<?php
/**
 * @author Sergey Palamarchuk
 * @email pase@ciklum.com
 */

namespace LifeSafe4U\UserBundle\Security;

use LifeSafe4U\UserBundle\Model\UserManager;
use LifeSafe4U\UserBundle\Security\Exception\DeletedAccountException;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * Constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userManager->findUserByEmail($username);

        if (!$user instanceof AdvancedUserInterface) {
            throw new UsernameNotFoundException(sprintf('No user with email "%s" was found.', $username));
        }

        $account = $user->getAccount();
        if ($account && !$account->isAccountEnabled()) {
            $ex = new AccountExpiredException('User account has expired.');
            $ex->setUser($user);
            throw $ex;
        }

        if ($account && $account->isDeleted()) {
            $ex = new DeletedAccountException('User account is deleted.');
            $ex->setUser($user);
            throw $ex;
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        $refreshedUser = $this->userManager->findUserBy(['id' => $user->getId()]);
        if (null === $refreshedUser) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $refreshedUser;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === $this->userManager->getClass();
    }
}

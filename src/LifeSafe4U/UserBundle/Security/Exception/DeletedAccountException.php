<?php

/**
 * @author tiger
 * Date: 29.10.14
 * Time: 10:07
 */

namespace LifeSafe4U\UserBundle\Security\Exception;

use Symfony\Component\Security\Core\Exception\AccountStatusException;

class DeletedAccountException extends AccountStatusException
{

}

<?php

namespace Ens\LunchBundle\Security;

use FR3D\LdapBundle\Ldap\LdapManager as BaseLdapManager;
use Symfony\Component\Security\Core\User\UserInterface;

class LdapManager extends BaseLdapManager
{
    protected $entityManager;

    public function __construct($driver, $userManager, $params, $entityManager)
    {
        parent::__construct($driver, $userManager, $params);

        $this->entityManager = $entityManager;
    }

    protected function hydrate(UserInterface $user, array $entry)
    {
        parent::hydrate($user, $entry);

        $allUsers = $this->entityManager->getRepository(
            'EnsLunchBundle:User'
        )->findAll();

        $searchUser = $this->entityManager->getRepository(
            'EnsLunchBundle:User'
        )->findOneBy(array('username' => $user->getUsername()));

        if (count($allUsers) == 0 ||($searchUser && in_array('ROLE_ADMIN', $searchUser->getRoles()))) {
            $user->addRole('ROLE_ADMIN');
        }

    }
}
<?php
namespace Ens\LunchBundle\Security;

use FOS\UserBundle\Model\UserInterface;

class UserManager extends \FOS\UserBundle\Doctrine\UserManager{

    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $newUser = $this->repository->findOneBy(['email'=>$user->getEmail()]);

        if ($newUser) $user = $newUser;

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
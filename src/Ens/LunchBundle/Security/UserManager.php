<?php
namespace Ens\LunchBundle\Security;

use FOS\UserBundle\Model\UserInterface;

class UserManager extends \FOS\UserBundle\Doctrine\UserManager{

    public function updateUser(UserInterface $user, $andFlush = true)
    {

        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $user = $this->repository->findOneBy(['email'=>$user->getEmail()]);

        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
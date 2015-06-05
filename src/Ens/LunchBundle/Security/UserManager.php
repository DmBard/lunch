<?php
namespace Ens\LunchBundle\Security;

use Doctrine\ORM\PersistentCollection;
use FOS\UserBundle\Model\UserInterface;

class UserManager extends \FOS\UserBundle\Doctrine\UserManager{

    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updateCanonicalFields($user);
        $this->updatePassword($user);

        $newUser = $this->repository->findOneBy(['email'=>$user->getEmail()]);
        /** @var PersistentCollection $allUsers */
        $allUsers = $this->repository->findAll();

        if(!$allUsers){
            $user->addRole(UserInterface::ROLE_SUPER_ADMIN);
            //var_dump($user, 'ADMIN');die;
            $this->objectManager->persist($user);
            $this->objectManager->flush();
            return;
        } elseif ($newUser) {
//            var_dump($user, 'new');
            $user = $newUser;
        }
//        var_dump('default');die;


        $this->objectManager->persist($user);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
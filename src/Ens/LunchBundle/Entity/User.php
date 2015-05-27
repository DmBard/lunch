<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 */
class User extends BaseUser
{
    protected $id;

//    /**
//     * @return \DateTime
//     */
//    public function getExpiresAt()
//    {
//        return $this->expiresAt;
//    }

//    /**
//     * @param \DateTime $expiresAt
//     */
//    public function setExpiresAt($expiresAt)
//    {
//        $this->expiresAt = $expiresAt;
//    }
//
//    /**
//     * @return \DateTime
//     */
//    public function getCredentialsExpireAt()
//    {
//        return $this->credentialsExpireAt;
//    }

//    /**
//     * @param \DateTime $credentialsExpireAt
//     */
//    public function setCredentialsExpireAt($credentialsExpireAt)
//    {
//        $this->credentialsExpireAt = $credentialsExpireAt;
//    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function __construct()
    {
        parent::__construct();
        // your own logic
    }
}

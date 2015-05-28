<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Jointable
 */
class Jointable
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $id_user;

    /**
     * @var integer
     */
    private $id_lunch;

    /** @var  boolean */
    private $active;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id_user
     *
     * @param integer $idUser
     * @return Jointable
     */
    public function setIdUser($idUser)
    {
        $this->id_user = $idUser;

        return $this;
    }

    /**
     * Get id_user
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * Set id_lunch
     *
     * @param integer $idLunch
     * @return Jointable
     */
    public function setIdLunch($idLunch)
    {
        $this->id_lunch = $idLunch;

        return $this;
    }

    /**
     * Get id_lunch
     *
     * @return integer 
     */
    public function getIdLunch()
    {
        return $this->id_lunch;
    }
}
<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Ens\LunchBundle\Repository\Jointable")
 * @ORM\Table(name="jointable")
 */
class Jointable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $userName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $id_lunch;

    /**
     * @ORM\Column(type="boolean")
     */
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
    public function setUserName($idUser)
    {
        $this->userName = $idUser;

        return $this;
    }

    /**
     * Get id_user
     *
     * @return integer 
     */
    public function getUserName()
    {
        return $this->userName;
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

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}

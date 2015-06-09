<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use FR3D\LdapBundle\Model\LdapUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_table")
 */
class User extends BaseUser implements LdapUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", )
     */
    private $defaultAction = 1; //1-delete mode; 2-random mode; 3-previous choice

    /**
     * @ORM\Column(type="string", length=255 )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255 )
     */
    private $floor = 'floor_4';

    private $dn;

    public function __construct()
    {
        parent::__construct();
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }
    }

    /**
     * @return mixed
     */
    public function getFloor()
    {
        return $this->floor;
    }

    /**
     * @param mixed $floor
     */
    public function setFloor($floor)
    {
        $this->floor = $floor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
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
     * Set Ldap Distinguished Name.
     *
     * @param string $dn Distinguished Name
     */
    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    /**
     * Get Ldap Distinguished Name.
     *
     * @return string Distinguished Name
     */
    public function getDn()
    {
        return $this->dn;
    }

    /**
     * @return mixed
     */
    public function getDefaultAction()
    {
        return $this->defaultAction;
    }

    /**
     * @param mixed $defaultAction
     */
    public function setDefaultAction($defaultAction)
    {
        $this->defaultAction = $defaultAction;
    }
}

<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use IMAG\LdapBundle\User\LdapUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 */
class User extends BaseUser implements LdapUserInterface
{
    /** @var  string */
    protected $name;

    /** @var  string */
    protected $surname;

    protected $id;

    private $dn;

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
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }


    public function __construct()
    {
        parent::__construct();
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }
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
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        // TODO: Implement isEqualTo() method.
    }

    public function getDn()
    {
        return $this->dn;
    }

    public function setDn($dn)
    {
        $this->dn = $dn;
    }

    public function getCn()
    {
        // TODO: Implement getCn() method.
    }

    public function setCn($cn)
    {
        // TODO: Implement setCn() method.
    }

    public function getAttributes()
    {
        // TODO: Implement getAttributes() method.
    }

    public function setAttributes(array $attributes)
    {
        // TODO: Implement setAttributes() method.
    }

    public function getAttribute($name)
    {
        // TODO: Implement getAttribute() method.
    }
}

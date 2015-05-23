<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 */
class Person
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $lunches;

    /**
     * Constructor
     */

    private $active_lunches;



    public function __construct()
    {
        $this->lunches = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getActiveLunches()
    {
        return $this->active_lunches;
    }

    public function setActiveLunches($active_lunches)
    {
        $this->active_lunches = $active_lunches;
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
     * Set name
     *
     * @param string $name
     * @return Person
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add lunches
     *
     * @param \Ens\LunchBundle\Entity\Lunch $lunches
     * @return Person
     */
    public function addLunch(\Ens\LunchBundle\Entity\Lunch $lunches)
    {
        $this->lunches[] = $lunches;

        return $this;
    }

    /**
     * Remove lunches
     *
     * @param \Ens\LunchBundle\Entity\Lunch $lunches
     */
    public function removeLunch(\Ens\LunchBundle\Entity\Lunch $lunches)
    {
        $this->lunches->removeElement($lunches);
    }

    /**
     * Get lunches
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLunches()
    {
        return $this->lunches;
    }
    
    public function __toString()
    {
	return $this->getName() ? $this->getName() : "";
    }
}

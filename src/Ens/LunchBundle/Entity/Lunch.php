<?php

namespace Ens\LunchBundle\Entity;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lunch
 */
class Lunch
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var integer
     */
    private $count;

    /**
     * @var string
     *
     */
    private $description;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var string
     */
    private $day;

    /**
     * @var string
     */
    private $categories;

    /**
     * @var User
     */
    private $user;

    public $file;

    /**
     * @var  boolean
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * @param string $day
     */
    public function setDay($day)
    {
        $this->day = $day;
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
     * Set count
     *
     * @param integer $count
     * @return Lunch
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count
     *
     * @return integer
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Lunch
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Lunch
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Lunch
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        if (!$this->getCreatedAt()) {
            $this->created_at = new \DateTime();
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtValue()
    {
        $this->updated_at = new \DateTime();
    }

    /**
     * Set categories
     *
     * @param string $categories
     * @return Lunch
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * Get categories
     *
     * @return string
     */
    public function getCategories()
    {
        return $this->categories;
    }

    public static function getListCategories()
    {
        return array('Salad' => 'Salad', 'Soup' => 'Soup', 'Main_Course' => 'Main_Course', 'Dessert' => 'Dessert');
    }

    public static function getListCategoriesValues()
    {
        return array_keys(self::getListCategories());
    }

    public static function getListDays()
    {
        return array(
            'Monday' => 'Monday',
            'Tuesday' => 'Tuesday',
            'Wednesday' => 'Wednesday',
            'Thursday' => 'Thursday',
            'Friday' => 'Friday'
        );
    }

    public static function getListDaysValues()
    {
        return array_keys(self::getListDays());
    }

    protected function getUploadDir()
    {
        return 'uploads/file';
    }

    protected function getUploadRootDir()
    {
        return '/web/sites/lunch.local/web/'.$this->getUploadDir();
    }

    public function getWebPath()
    {
        return $this->getUploadDir().'/'.'menu';
    }

    public function getAbsolutePath()
    {
        return $this->getUploadRootDir().'/'.'menu';
    }

    /**
     * @ORM\PostPersist
     */
    public function upload()
    {
//        if (null === $this->file) {
//            return;
//        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error

        unset($this->file);
    }

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
//        if ($file = $this->getAbsolutePath()) {
//            unlink($file);
//        }
    }

}

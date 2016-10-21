<?php

namespace UserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * If user has validate email
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed;


    /**
     * @return boolean
     */
    public function isConfirmed() : bool
    {
        return $this->confirmed;
    }

    /**
     * @param boolean $confirmed
     */
    public function setConfirmed(bool $confirmed)
    {
        $this->confirmed = $confirmed;
    }
}

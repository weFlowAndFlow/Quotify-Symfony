<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 */
class ChangePassword
{

    /**
     *
     */
    private $oldPassword;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(min=8)
     */
    private $newPassword;

    /**
     * @return mixed
     */
    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    /**
     * @param mixed $oldPassword
     */
    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    /**
     * @return mixed
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param mixed $newPassword
     */
    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }


}

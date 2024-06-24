<?php

namespace App\Blog\Entity;

use DateTime;
use Exception;

class Post
{

    public $id;

    public $name;

    public $slug;

    public $content;

    public $createdAt;

    public $updatedAt;

    /**
     * @throws Exception
     */
    public function setCreatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        }
    }

    /**
     * @throws Exception
     */
    public function setUpdatedAt($datetime)
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        }
    }
}

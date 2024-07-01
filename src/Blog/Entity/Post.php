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

    public $image;

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

    public function getThumb(): string
    {
        ['fileName' => $fileName, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $fileName . '_thumb.' . $extension;
    }
}

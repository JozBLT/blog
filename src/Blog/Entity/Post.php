<?php

namespace App\Blog\Entity;

use DateTime;
use Exception;

class Post
{

    public int $id;

    public ?string $name = null;

    public ?string $slug = null;

    public ?string $content = null;

    public DateTime $createdAt;

    public DateTime $updatedAt;

    public ?string $image = null;

    /** @throws Exception */
    public function setCreatedAt(DateTime|string $datetime): void
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        } else {
            $this->createdAt = $datetime;
        }
    }

    /** @throws Exception */
    public function setUpdatedAt(DateTime|string $datetime): void
    {
        if (is_string($datetime)) {
            $this->updatedAt = new DateTime($datetime);
        } else {
            $this->updatedAt = $datetime;
        }
    }

    public function getThumb(): string
    {
        ['filename' => $fileName, 'extension' => $extension] = pathinfo($this->image);
        return '/uploads/posts/' . $fileName . '_thumb.' . $extension;
    }

    public function getImageUrl(): string
    {
        return '/uploads/posts/' . $this->image;
    }
}

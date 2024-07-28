<?php

namespace App\Comment\Entity;

use DateTime;
use Exception;

class Comment
{

    public int $id;

    public int $postId;

    public string $username;

    public string $comment;

    public DateTime $createdAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function setComment(string $comment): void
    {
        $this->comment = $comment;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /** @throws Exception */
    public function setCreatedAt(DateTime|string $datetime): void
    {
        if (is_string($datetime)) {
            $this->createdAt = new DateTime($datetime);
        } else {
            $this->createdAt = $datetime;
        }
    }
}

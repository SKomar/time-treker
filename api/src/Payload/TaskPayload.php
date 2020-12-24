<?php


namespace App\Payload;


use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class TaskPayload
{
    /**
     * @Assert\NotBlank
     */
    private string $title;

    /**
     * @Assert\NotBlank
     */
    private string $comment;

    /**
     * @Assert\NotBlank
     */
    private string $date;

    /**
     * @Assert\NotBlank
     */
    private int $time_spent;

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     * @return $this
     */
    public function setDate(string $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimeSpent(): int
    {
        return $this->time_spent;
    }

    /**
     * @param int $time_spent
     * @return $this
     */
    public function setTimeSpent(int $time_spent): self
    {
        $this->time_spent = $time_spent;
        return $this;
    }
}
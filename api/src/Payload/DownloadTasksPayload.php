<?php


namespace App\Payload;


use Symfony\Component\Validator\Constraints as Assert;

class DownloadTasksPayload
{
    /**
     * @var string A "Y-m-d" formatted value
     * @Assert\NotBlank()
     * @Assert\Date
     */
    private string $from;

    /**
     * @var string A "Y-m-d" formatted value
     * @Assert\NotBlank()
     * @Assert\Date()
     */
    private string $to;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Choice({"pdf", "csv", "xls"})
     */
    private string $format;

    /**
     * @param string $from
     * @return DownloadTasksPayload
     */
    public function setFrom(string $from): DownloadTasksPayload
    {
        $this->from = $from;
        return $this;
    }

    /**
     * @return string
     */
    public function getFrom(): string
    {
        return $this->from;
    }

    /**
     * @param string $to
     * @return DownloadTasksPayload
     */
    public function setTo(string $to): DownloadTasksPayload
    {
        $this->to = $to;
        return $this;
    }


    /**
     * @return string
     */
    public function getTo(): string
    {
        return $this->to;
    }

    /**
     * @param string $format
     * @return DownloadTasksPayload
     */
    public function setFormat(string $format): DownloadTasksPayload
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            'from'  =>  $this->getFrom(),
            'to'    =>  $this->getTo(),
            'format' => $this->getFormat(),
        ];
    }
}
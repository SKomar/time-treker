<?php


namespace App\Serializer;


use App\Payload\DownloadTasksPayload;
use Exception;
use JMS\Serializer\Serializer;
use RuntimeException;

class DownloadTasksPayloadSerializer
{
    /**
     * @var Serializer
     */
    private Serializer $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param string $payload
     *
     * @return DownloadTasksPayload
     */
    public function extract(string $payload): DownloadTasksPayload
    {
        try {
            return $this->serializer->deserialize($payload, DownloadTasksPayload::class, 'json');
        } catch (Exception $e) {
            throw new RuntimeException('Incorrect payload, expected json!' . $e->getMessage());
        }
    }
}
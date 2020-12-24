<?php


namespace App\Serializer;

use App\Payload\TaskPayload;
use Exception;
use JMS\Serializer\Serializer;
use RuntimeException;

class TaskPayloadSerializer
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
     * @return TaskPayload
     */
    public function extract(string $payload): TaskPayload
    {
        try {
            return $this->serializer->deserialize($payload, TaskPayload::class, 'json');
        } catch (Exception $e) {
            throw new RuntimeException('Incorrect payload, expected json!' . $e->getMessage());
        }
    }
}
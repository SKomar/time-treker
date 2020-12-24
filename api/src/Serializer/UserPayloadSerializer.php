<?php


namespace App\Serializer;

use App\Payload\UserPayload;
use Exception;
use JMS\Serializer\Serializer;
use RuntimeException;

class UserPayloadSerializer
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
     * @return UserPayload
     */
    public function extract(string $payload): UserPayload
    {
        try {
            return $this->serializer->deserialize($payload, UserPayload::class, 'json');
        } catch (Exception $e) {
            throw new RuntimeException('Incorrect payload, expected json: {"email":"some@email.com","password":"some-password"}');
        }
    }
}
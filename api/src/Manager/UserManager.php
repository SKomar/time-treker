<?php


namespace App\Manager;


use App\Entity\User;
use App\Serializer\UserPayloadSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserManager
{
    /**
     * @var UserPayloadSerializer
     */
    private UserPayloadSerializer $userPayloadSerializer;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    public function  __construct(
        UserPayloadSerializer $userPayloadSerializer,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $encoder,
        ValidatorInterface $validator
    ) {
        $this->userPayloadSerializer = $userPayloadSerializer;
        $this->entityManager = $entityManager;
        $this->encoder = $encoder;
        $this->validator = $validator;
    }

    /**
     * @param string $payload
     * @return User
     */
    public function register(string $payload): User
    {
        $userPayload = $this->userPayloadSerializer->extract($payload);

        $errors = $this->validator->validate($userPayload);
        if (count($errors) > 0) {
            throw new \RuntimeException('Invalid payload: ' . $errors);
        }

        $user = new User();
        $user
            ->setEmail($userPayload->getEmail())
            ->setUsername($userPayload->getUsername())
            ->setPassword(
                $this->encoder->encodePassword(
                    $user,
                    $userPayload->getPassword()
                )
            )
        ;

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }
}
<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                "email" => "david@mail.com",
                "roles"=> ['ROLE_MANAGER'],
                "password"=> "password123"
            ],
            [
                "email"=> "emma@mail.com",
                "roles"=> ['ROLE_MANAGER'],
                "password"=> "password123"
            ],
            [
                "email"=> "lucas@mail.com",
                "roles"=> ['ROLE_ADMIN'],
                "password"=> "password123"
            ],
            [
                "email"=> "sophie@mail.com",
                "roles"=> ['ROLE_MANAGERs'],
                "password"=> "password123"
            ],
            [
                "email"=> "liam@mail.com",
                "roles"=> ['ROLE_MANAGER'],
                "password"=> "password123"
            ]
                ];

        foreach ($users as $userData) {
            $user = new User();
            $user->setEmail($userData['email']);
            $user->setRoles($userData['roles']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPassword($hashedPassword);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
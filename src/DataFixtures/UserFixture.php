<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        for ($i = 0; $i < 15; $i++) {
            $user = new User();
            $user->setEmail("some_$i@example.com");
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'engage'));

            if($i > 12) {
                $user->setRoles(['ROLE_POSTER']);
            }

            $manager->persist($user);
            $this->addReference("user_$i", $user);
        }

        $manager->flush();
    }
}

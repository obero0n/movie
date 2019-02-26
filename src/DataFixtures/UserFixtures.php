<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{
  private $passwordEncoder;

     public function __construct(UserPasswordEncoderInterface $passwordEncoder)
     {
         $this->passwordEncoder = $passwordEncoder;
     }

    public function load(ObjectManager $manager)
    {
      $faker = Faker\Factory::create('fr_FR');
      for ($i=0; $i < 10; $i++) {
        $user = new User();
        $user->setUsername($faker->lastName());
        $user->setPassword("password$i");
        $manager->persist($user);
      }
        $manager->flush();
    }
}

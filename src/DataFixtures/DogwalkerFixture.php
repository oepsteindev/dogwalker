<?php

namespace App\DataFixtures;

use App\Entity\DogWalker;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DogwalkerFixture extends Fixture
{
    public function load(ObjectManager $manager) :void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $walker = new Dogwalker();
            $walker->setName($faker->firstName . ' ' .  $faker->lastName);
            $walker->setEmail($faker->unique()->email);
            $walker->setPhoneNumber($faker->phoneNumber);
            $walker->setBio($faker->text);
            $walker->setMaxDistance($faker->numberBetween(1, 100));
            $walker->setZipCode($faker->postcode);


            $manager->persist($walker);
        }

        $manager->flush();
    }
}

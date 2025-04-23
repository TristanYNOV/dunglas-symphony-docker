<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Song;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct() {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $artistes = ['KungFu Minou', 'Graoutesque', 'BigMinou', 'MC Graou', 'Le Chat Mystique', 'MinouMan'];

        for ($i = 0; $i < 100; $i++) {
            $song = new Song();

            $themeWord = ucfirst($this->faker->streetName());
            $place = ucfirst($this->faker->city());
            $style = $this->faker->randomElement(['Graou', 'Minou', 'Meow', 'Purr']);

            $title = "{$style} {$themeWord} in the {$place}";

            $artist = $this->faker->randomElement($artistes);

            $song->setName(name: $title);
            $song->setArtiste(artiste: $artist);

            $manager->persist(object: $song);
        }

        $manager->flush();
    }
}

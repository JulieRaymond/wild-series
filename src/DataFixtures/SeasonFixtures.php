<?php

namespace App\DataFixtures;

use App\Entity\Season;
use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        $programs = $manager->getRepository(\App\Entity\Program::class)->findAll();

        foreach ($programs as $program) {
            for ($i = 1; $i <= 5; $i++) {
                $season = new Season();
                $season->setNumber($i);
                $season->setYear($faker->year());
                $season->setDescription($faker->paragraph);
                $season->setProgram($program);

                $manager->persist($season);

                // Create 10 episodes for each season
                for ($j = 1; $j <= 10; $j++) {
                    $episode = new Episode();
                    $episode->setTitle($faker->sentence);
                    $episode->setNumber($j);
                    $episode->setSynopsis($faker->paragraph);
                    $episode->setSeason($season);
                    $episode->setDuration($faker->numberBetween(20, 60));

                    // Generate slug using the SluggerInterface
                    $slug = $this->slugger->slug($episode->getTitle())->lower();
                    $slug = str_replace([' ', '_'], '-', $slug);
                    $episode->setSlug($slug);

                    $manager->persist($episode);
                }
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProgramFixtures::class,
        ];
    }
}

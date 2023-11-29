<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Séries
        $series = [
            'WalkingDead',
            'BreakingBad',
            'StrangerThings',
            'Friends',
            'Inception',
            'TheDarkKnight',
            'PulpFiction',
            'TheMatrix',
            'ForrestGump',
            'ToyStory',
        ];

        foreach ($series as $seriesReference) {
            // Deux saisons pour chaque série
            for ($seasonNumber = 1; $seasonNumber <= 2; $seasonNumber++) {
                // Deux épisodes pour chaque saison
                $this->createEpisode($manager, $seriesReference, $seasonNumber, 'Épisode 1');
                $this->createEpisode($manager, $seriesReference, $seasonNumber, 'Épisode 2');
            }
        }

        // Appliquer les changements dans la base de données
        $manager->flush();
    }

    private function createEpisode(ObjectManager $manager, string $programReference, int $seasonNumber, string $episodeTitle): void
    {
        $episode = new Episode();
        $episode->setTitle($episodeTitle);
        $episode->setNumber(1); // Vous pouvez ajuster le numéro d'épisode selon vos besoins
        $episode->setSynopsis('Synopsis de ' . $episodeTitle);
        $episode->setSeason($this->getReference('season' . $seasonNumber . '_' . $programReference));

        $manager->persist($episode);
    }

    public function getDependencies(): array
    {
        return [
            SeasonFixtures::class,
        ];
    }
}

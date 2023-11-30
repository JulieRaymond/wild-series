<?php
namespace App\DataFixtures;

use App\Entity\Season;
use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
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
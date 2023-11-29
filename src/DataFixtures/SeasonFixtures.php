<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
public function load(ObjectManager $manager): void
{
    // Série : Walking Dead
    $this->createSeason($manager, 'WalkingDead', 1, 2021, 'Des morts-vivants menacent la survie d\'un groupe de survivants.');
    $this->createSeason($manager, 'WalkingDead', 2, 2022, 'Les survivants luttent contre de nouveaux dangers dans un monde post-apocalyptique.');
    $this->createSeason($manager, 'WalkingDead', 3, 2023, 'Les tensions s\'intensifient alors que les survivants cherchent un refuge sûr.');

    // Série : Breaking Bad
    $this->createSeason($manager, 'BreakingBad', 1, 2020, 'Un professeur de chimie devient un producteur de méthamphétamine.');
    $this->createSeason($manager, 'BreakingBad', 2, 2021, 'Les conséquences de la vie criminelle de Walter White s\'aggravent.');
    $this->createSeason($manager, 'BreakingBad', 3, 2022, 'La montée en puissance du cartel menace la sécurité de Walter et Jesse.');

    // Série : Stranger Things
    $this->createSeason($manager, 'StrangerThings', 1, 2019, 'Des événements surnaturels secouent une petite ville, avec l\'apparition d\'un monde parallèle.');
    $this->createSeason($manager, 'StrangerThings', 2, 2020, 'Les enfants se retrouvent confrontés à de nouveaux mystères et à des créatures plus dangereuses.');
    $this->createSeason($manager, 'StrangerThings', 3, 2021, 'Une menace encore plus sombre émerge alors que les liens entre les mondes se renforcent.');

    // Série : Friends
    $this->createSeason($manager, 'Friends', 1, 1994, 'Les aventures hilarantes et les relations de six amis new-yorkais.');
    $this->createSeason($manager, 'Friends', 2, 1995, 'Les liens d\'amitié sont mis à l\'épreuve à mesure que les amis traversent des hauts et des bas.');
    $this->createSeason($manager, 'Friends', 3, 1996, 'Des moments comiques et émouvants continuent de définir la vie des amis.');

    // Série : Inception
    $this->createSeason($manager, 'Inception', 1, 2010, 'Un voleur de rêves est engagé pour une mission complexe impliquant l\'infiltration des rêves.');
    $this->createSeason($manager, 'Inception', 2, 2011, 'Les limites de la réalité et du rêve s\'entremêlent davantage dans des scénarios époustouflants.');
    $this->createSeason($manager, 'Inception', 3, 2012, 'Le passé trouble des personnages influence leurs voyages dans des rêves toujours plus profonds.');

    // Série : The Dark Knight
    $this->createSeason($manager, 'TheDarkKnight', 1, 2018, 'Batman se lève pour protéger Gotham City contre une nouvelle menace.');
    $this->createSeason($manager, 'TheDarkKnight', 2, 2019, 'Le Joker sème le chaos, testant la résilience de Batman et de la ville.');
    $this->createSeason($manager, 'TheDarkKnight', 3, 2020, 'De nouveaux ennemis émergent alors que Batman est confronté à des dilemmes moraux difficiles.');

    // Série : Pulp Fiction
    $this->createSeason($manager, 'PulpFiction', 1, 1996, 'Des histoires de criminels s\'entremêlent dans un récit non linéaire à Los Angeles.');
    $this->createSeason($manager, 'PulpFiction', 2, 1997, 'Les conséquences des actions passées hantent les personnages, conduisant à des retournements surprenants.');
    $this->createSeason($manager, 'PulpFiction', 3, 1998, 'De nouveaux personnages et événements se connectent aux intrigues existantes, créant un réseau complexe.');

    // Série : The Matrix
    $this->createSeason($manager, 'TheMatrix', 1, 1999, 'Un pirate informatique découvre la vérité sur la réalité, lançant une rébellion.');
    $this->createSeason($manager, 'TheMatrix', 2, 2000, 'Les enjeux s\'intensifient alors que la lutte entre les machines et les humains atteint son paroxysme.');
    $this->createSeason($manager, 'TheMatrix', 3, 2001, 'Les révélations finales et les batailles épiques définissent le destin de l\'humanité et de la matrice.');

    // Série : Forrest Gump
    $this->createSeason($manager, 'ForrestGump', 1, 1992, 'La vie extraordinaire de Forrest Gump, un homme simple, mais exceptionnel.');
    $this->createSeason($manager, 'ForrestGump', 2, 1993, 'Forrest continue de traverser des époques clés de l\'histoire américaine, influençant ceux qui l\'entourent.');
    $this->createSeason($manager, 'ForrestGump', 3, 1994, 'Des aventures émotionnelles et inoubliables continuent de définir le parcours de Forrest et de ses amis.');

    // Série : Toy Story
    $this->createSeason($manager, 'ToyStory', 1, 1995, 'Les jouets prennent vie dans des aventures hilarantes lorsque les humains ne sont pas là.');
    $this->createSeason($manager, 'ToyStory', 2, 1998, 'De nouveaux jouets et défis ajoutent une dimension passionnante à la vie des jouets.');
    $this->createSeason($manager, 'ToyStory', 3, 2010, 'Les jouets sont confrontés à des adieux poignants alors qu\'Andy part pour l\'université.');

    // Appliquer les changements dans la base de données
    $manager->flush();
}

private function createSeason(ObjectManager $manager, string $programReference, int $seasonNumber, int $year, string $description): void
{
    $season = new Season();
    $season->setNumber($seasonNumber);
    $season->setYear($year);
    $season->setDescription($description);
    $season->setProgram($this->getReference('program_' . $programReference));
    $manager->persist($season);

    // Ajouter des références pour les Saisons créées
    $this->addReference('season' . $seasonNumber . '_' . $programReference, $season);
}

public function getDependencies(): array
{
    return [
    ProgramFixtures::class,
    // Ajouter d'autres fixtures dont Season dépend
    // ...
    ];
}
}

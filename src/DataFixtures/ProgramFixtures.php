<?php
namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager) :void
    {
        // Récupérer les références des catégories
        $categoryAction = $this->getReference('category_Action');
        $categoryComedy = $this->getReference('category_Comédie');
        $categoryDrama = $this->getReference('category_Drame');
        $categoryHorror = $this->getReference('category_Horreur');
        $categorySciFi = $this->getReference('category_Science-fiction');
        $categoryAdventure = $this->getReference('category_Aventure');
        $categoryRomance = $this->getReference('category_Romance');
        $categoryFantasy = $this->getReference('category_Fantaisie');
        $categoryThriller = $this->getReference('category_Thriller');
        $categoryAnimation = $this->getReference('category_Animation');

        // Créer des instances de Program avec différentes catégories
        $program1 = new Program();
        $program1->setTitle('Walking Dead');
        $program1->setSynopsis('Des zombies envahissent la terre');
        $program1->setCategory($categoryHorror);
        $manager->persist($program1);

        $program2 = new Program();
        $program2->setTitle('Breaking Bad');
        $program2->setSynopsis('Un professeur de chimie devient trafiquant de méthamphétamine');
        $program2->setCategory($categoryDrama);
        $manager->persist($program2);

        $program3 = new Program();
        $program3->setTitle('Stranger Things');
        $program3->setSynopsis('Des phénomènes étranges se produisent dans une petite ville');
        $program3->setCategory($categorySciFi);
        $manager->persist($program3);

        $program4 = new Program();
        $program4->setTitle('Friends');
        $program4->setSynopsis('Un groupe de copains à NewYork');
        $program4->setCategory($categoryComedy);
        $manager->persist($program4);

        $program5 = new Program();
        $program5->setTitle('Inception');
        $program5->setSynopsis('Un voleur de rêves doit accomplir un dernier travail');
        $program5->setCategory($categoryAdventure);
        $manager->persist($program5);

        $program6 = new Program();
        $program6->setTitle('The Dark Knight');
        $program6->setSynopsis('Batman affronte le Joker pour protéger Gotham City');
        $program6->setCategory($categoryAction);
        $manager->persist($program6);

        $program7 = new Program();
        $program7->setTitle('Pulp Fiction');
        $program7->setSynopsis('Les histoires de plusieurs criminels interconnectées à Los Angeles');
        $program7->setCategory($categoryThriller);
        $manager->persist($program7);

        $program8 = new Program();
        $program8->setTitle('The Matrix');
        $program8->setSynopsis('Un pirate informatique découvre la vérité sur la réalité');
        $program8->setCategory($categoryFantasy);
        $manager->persist($program8);

        $program9 = new Program();
        $program9->setTitle('Forrest Gump');
        $program9->setSynopsis('La vie extraordinaire de Forrest Gump');
        $program9->setCategory($categoryRomance);
        $manager->persist($program9);

        $program10 = new Program();
        $program10->setTitle('Toy Story');
        $program10->setSynopsis('Les jouets prennent vie quand les humains ne sont pas là');
        $program10->setCategory($categoryAnimation);
        $manager->persist($program10);

        // Appliquer les changements dans la base de données
        $manager->flush();

        // Ajouter des références pour les Programmes créés
        $this->addReference('program_WalkingDead', $program1);
        $this->addReference('program_BreakingBad', $program2);
        $this->addReference('program_StrangerThings', $program3);
        $this->addReference('program_Friends', $program4);
        $this->addReference('program_Inception', $program5);
        $this->addReference('program_TheDarkKnight', $program6);
        $this->addReference('program_PulpFiction', $program7);
        $this->addReference('program_TheMatrix', $program8);
        $this->addReference('program_ForrestGump', $program9);
        $this->addReference('program_ToyStory', $program10);
    }
    public function getDependencies() :array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

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
        $program5->setTitle('The Dark Knight');
        $program5->setSynopsis('Batman affronte le Joker pour protéger Gotham City');
        $program5->setCategory($categoryAction);
        $manager->persist($program5);

        // Appliquer les changements dans la base de données
        $manager->flush();

        // Ajouter des références pour les Programmes créés
        $this->addReference('program_WalkingDead', $program1);
        $this->addReference('program_BreakingBad', $program2);
        $this->addReference('program_StrangerThings', $program3);
        $this->addReference('program_Friends', $program4);
        $this->addReference('program_TheDarkKnight', $program5);
    }
    public function getDependencies() :array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

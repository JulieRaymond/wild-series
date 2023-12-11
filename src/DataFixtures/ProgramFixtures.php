<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Récupérer les références des catégories
        $categoryAction = $this->getReference('category_Action');
        $categoryComedy = $this->getReference('category_Comédie');
        $categoryDrama = $this->getReference('category_Drame');
        $categoryHorror = $this->getReference('category_Horreur');
        $categorySciFi = $this->getReference('category_Science-fiction');

        // Créer des instances de Program avec différentes catégories et les URL des posters
        $programsData = [
            ['title' => 'Walking Dead', 'synopsis' => 'Des zombies envahissent la terre', 'category' => $categoryHorror, 'poster' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS3XFRQrxoEU1U88IHEEUcPTHYwVetHiaI0fA8L9CD98_E6fJiJ3e_IK-X17BbHdsEo5f8&usqp=CAU'],
            ['title' => 'Breaking Bad', 'synopsis' => 'Un professeur de chimie devient trafiquant de méthamphétamine', 'category' => $categoryDrama, 'poster' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7x2UNKLMUDSyNeg21WWY2vKm02PlZgtLd11oLAAiBGhBiPOWmCMVQW-QtV61CAmCzWJg&usqp=CAU'],
            ['title' => 'Stranger Things', 'synopsis' => 'Des phénomènes étranges se produisent dans une petite ville', 'category' => $categorySciFi, 'poster' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQUrlkJc0I8tmbSfxhZ6voDqWR0vO_hdmLofdZK-387UgFFi_77JXF2i55BT3uvbQPkbgw&usqp=CAU'],
            ['title' => 'Friends', 'synopsis' => 'Un groupe de copains à New York', 'category' => $categoryComedy, 'poster' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBF2BZ2Iu8VyyuGbJ0T_Vo33WfZHu4p1KSiWlatQO0876mi8x6w1Ypu1IZXzjOFAvtGbk&usqp=CAU'],
            ['title' => 'The Dark Knight', 'synopsis' => 'Batman affronte le Joker pour protéger Gotham City', 'category' => $categoryAction, 'poster' => 'https://www.lexpress.fr/resizer/YdCVV6zKSgbzVxz1kVzRRyCSjyw=/1200x630/cloudfront-eu-central-1.images.arcpublishing.com/lexpress/XCXQPBH62JBWHGOKHQLXLWFEFU.jpg'],
        ];

        foreach ($programsData as $programData) {
            $program = new Program();
            $program->setTitle($programData['title']);
            $program->setSynopsis($programData['synopsis']);
            $program->setCategory($programData['category']);
            $program->setPoster($programData['poster']);

            // Utilisation du SluggerInterface pour générer un slug à partir du titre
            $slug = $this->slugger->slug($program->getTitle())->lower();
            $slug = str_replace(' ', '-', $slug);
            $program->setSlug($slug);

            $manager->persist($program);
        }

        // Appliquer les changements dans la base de données
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}

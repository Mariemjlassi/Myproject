<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\Livres;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker=Factory::create('fr_FR');
        for($j=1;$j<=3;$j++){
            $cat=new Categories();
            $cat->setLibelle($faker->name())
                ->setSlug($faker->name())
                ->setDescripton($faker->paragraph(3));
            $manager->persist($cat);
        
        for($i=1;$i<=random_int(50,100);$i++){
            
        $livre = new Livres();
        $titre=$faker->name();
        $livre->setEditeur($faker->userName())
            ->setDateEdition($faker->dateTime())
            ->setTitre($titre)
            ->setQte($faker->numberBetween(0,200))
            ->setResume($faker->sentence(4))
            ->setSlug(strtolower(preg_replace('/[^a-zA-Z0]/','-',$titre)))
            ->setPrix($faker->numberBetween(10,150))
            ->setImage($faker->imageUrl())
            ->setIsbn($faker->isbn13())
            ->setCategorie($cat);
        $manager->persist($livre);
            //persiste tout les objets
        
        }
    
        $manager->flush();
        //une seule requete envoyer au serveur
        
    }
}
}
//datetime=$faker->dateTime()  $datetimeimmut=\datetimeimmut::createFromMutable($datetime)
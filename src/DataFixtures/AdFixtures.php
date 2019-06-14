<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AdFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        
        //Création de mes 50 annonces
        for($i = 1; $i <= 50; $i++)
        {
            $ad = new Ad();

            $ad->setTitle($faker->sentence())
                ->setPrice($faker->numberBetween($min = 100, $max = 1000))
                ->setIntroduction($faker->paragraph(2))
                ->setContent('<p>'.join($faker->paragraphs(5), '</p><p>').'</p>')
                ->setCoverImage($faker->imageUrl(1000, 400))
                ->setRooms($faker->numberBetween($min = 1, $max = 8));
            
            //Création de mes images en fonction de mes annonces
            for($j = 1; $j < mt_rand(2, 5); $j++)
            {
                $image = new Image();

                $image->setUrl($faker->imageUrl())
                        ->setCaption($faker->sentence())
                        ->setAd($ad);

                $manager->persist($image);
            }
            $manager->persist($ad);
        }
        $manager->flush();
    }
}

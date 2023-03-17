<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use App\Entity\Category;
use App\Entity\Logement;
use App\Entity\Publication;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     * Hasheur de mdp
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'un objet Faker
        $faker = Factory::create('fr_FR');

        $hosting = "logement";
        $aide = "aide";
        $emploi = "emploi";

        // Création de 3 categories et persist
        $manager->persist((new Category())->setTitle($hosting));
        $manager->persist((new Category())->setTitle($aide));
        $manager->persist((new Category())->setTitle($emploi));


        // Création d'adresse
        for ($a=0; $a < 200; $a++) { 
            $adresse = new Adresse();

            $adresse->setStreet($faker->streetAddress())
                ->setCity($faker->city())
                ->setPostcode($faker->postcode());

            $manager->persist($adresse);
        }

        // Création logement
        for ($l=0; $l < 300; $l++) { 
            $logement = new Logement();

            $logement->setPrice($faker->randomNumber(3, true))
                ->setSurface($faker->randomFloat(2,9,70));

            $manager->persist($logement);
        }

        // On push les catégories en BDD
        $manager->flush();

        // Récupération des catégories et des adresses créées
        $allCategories = $manager->getRepository(Category::class)->findAll();
        $allAdresses = $manager->getRepository(Adresse::class)->findAll();
        $allLogments = $manager->getRepository(Logement::class)->findAll();

        // Création entre 15 et 30 Articles aléatoirement
        for ($p = 0; $p < mt_rand(150, 300); $p++) {

            //Création d'un nouvel objet Publication
            $publlication = new Publication();

            // On nourrit l'objet Publication
            $publlication->setTitle($faker->sentence(6))
                ->setDescription($faker->paragraph(10))
                ->setCategory($faker->randomElement($allCategories))
                ->setAdresse($faker->randomElement($allAdresses))
                ->setCreatedAt(new \DateTimeImmutable());
            if ( $publlication->getCategory()->getTitle() == 'logement'){
                $publlication->setLogement($faker->randomElement($allLogments));
            }

            // On fait persister l'objet
            $manager->persist($publlication);
        }

        // Création de 5 utilisateurs
        for ($u=0; $u < 5; $u++) { 
            // Création d'un nouvel objet User
            $user = new User();

            // Hashage de notre mdp avec les paramètres de sécurité de notre $user
            // dans config/packages/security.yaml
            $hash = $this->hasher->hashPassword($user, 'password');
            
            // Si premier utilisateur créé on lui donne le role admin
            // On nourrit l'objet User
            if($u === 0){
                $user->setRoles(["ROLE_ADMIN"])
                    ->setEmail("admin@test.test");
            } else {
                $user->setEmail($faker->safeEmail());
            }
            
            $user->setPassword($hash);

            // On fait persister l'objet
            $manager->persist($user);
        }

        // On envoie le tout en BDD
        $manager->flush();
    }
}
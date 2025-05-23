<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Pret;
use App\Entity\Livre;
use App\Entity\Adherent;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $manager;
    private $faker;
    private $repoLivre;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->faker = Factory::create('fr_FR');
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->repoLivre = $this->manager->getRepository(Livre::class);
        $this->loadAdherent();
        $this->loadPret();

        $manager->flush();
    }

// création d adherent
    public function loadAdherent(): void
    {
        $genre = ['male', 'female'];
        $commune = [
            "78003", "78005", "78006", "78007", "78009", "78010", "78013", "78015", "78020", "78029",
            "78030", "78031", "78033", "78034", "78036", "78043", "78048", "78049", "78050", "78053", "78057",
            "78062", "78068", "78070", "78071", "78072", "78073", "78076", "78077", "78082", "78084", "78087",
            "78089", "78090", "78092", "78096", "78104", "78107", "78108", "78113", "78117", "78118"
        ];
        for ($i = 0; $i < 50; $i++) {
            $adherent = new Adherent();
            $adherent->setNom($this->faker->lastName);
            $adherent->setPrenom($this->faker->firstName($genre[mt_rand(0, 1)]));
            $adherent->setAdresse($this->faker->address);
            $adherent->setTelephone($this->faker->phoneNumber());
            $adherent->setCodeCommune($commune[mt_rand(0, sizeof($commune) - 1)]);
            $adherent->setMail($adherent->getNom()."@gmail.com");
            $adherent->setPassword($this->passwordEncoder->encodePassword($adherent, $adherent->getNom()));
            $this->addReference('adherent' . $i, $adherent);
            $this->manager->persist($adherent);
        }

            $adherent = new Adherent();
            $adherent->setNom("Ott");
            $adherent->setPrenom("Quentin");
            $adherent->setMail("admin@gmail.com");
            $adherent->setRoles([Adherent::ROLE_ADMIN]);
            $adherent->setPassword($this->passwordEncoder->encodePassword($adherent, $adherent->getNom()));
            $this->manager->persist($adherent);

            $adherent = new Adherent();
            $adherent->setNom("Durand");
            $adherent->setPrenom("Sophie");
            $adherent->setMail("manager@gmail.com");
            $adherent->setRoles([Adherent::ROLE_MANAGER]);
            $adherent->setPassword($this->passwordEncoder->encodePassword($adherent, $adherent->getNom()));
            $this->manager->persist($adherent);


        $this->manager->flush();
    }
// création des prêts
    public function loadPret(): void
    {
        for($i = 0; $i < 25; $i++) {                //pour chaque adhérent
            
            $max=mt_rand(1,5);
            for($j = 0; $j <= $max; $j++) {     //création des prêts
                $pret = new Pret();
                $livre = $this->repoLivre->find(mt_rand(1,49));
                $pret->setLivre($livre);
                $pret->setAdherent($this->getReference('adherent' . $i));
                $pret->setDatePret($this->faker->dateTimeBetween('-6 months'));
                $dateRetourPrevue=date('Y-m-d H:m:s',strtotime('15 days', $pret->getDatePret()->getTimestamp()));
                $dateRetourPrevue =\DateTime::createFromFormat('Y-m-d H:m:s', $dateRetourPrevue);
                $pret->setDateRetourPrevue($dateRetourPrevue);

                if(mt_rand(0,3) == 1) {
                    $pret->setDateRetourReelle($this->faker->dateTimeBetween($pret->getDatePret(),"+30 days"));
                }
                $this->manager->persist($pret);
            }
        }
        $this->manager->flush();
    }
}
;
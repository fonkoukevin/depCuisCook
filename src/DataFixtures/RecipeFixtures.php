<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private readonly SluggerInterface $slugger)
    {

    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));


        $ingredients = array_map(fn(string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name))),
            ["Farine",
                "Sucre",
                "Oeufs",
                "Beurre",
                "Lait",
                "levure chimique",
                "Sel",
                "Chocolat noir",
                "Pepites de chocolat",
                "Fruits secs (amandes, noix, etc.)",
                "Vanille",
                "Cannelle",
                "Fraise",
                "Banane",
                "Pomme",
                "Carotte",
                "Oignon",
                "Ail"]
        );

        $units = [
            "g",
            "kg",
            "L",
            "ml",
            "cl",
            "dL",
            "c. a soupe",
            "c. a cafe",
            "pincee",
            "verre"
        ];

        foreach($ingredients as $ingredient){
            $manager->persist($ingredient);
        }


        $categories = ['Plat chaud', 'Dessert', ' Entree', 'Gouter'];

        foreach ($categories as $c) {
            $category = (new Category())
                ->setName($c)
                ->setSlug(strtolower($this->slugger->slug($c)))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));

            $manager->persist($category);
            $this->addReference($c, $category);


        }

        for ($i = 1; $i <= 10; $i++) {
            $title = $faker->foodName();
            $recipe = (new Recipe())
                ->setTitre($title)
                ->setSlug(strtolower($this->slugger->slug($title)))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraphs(10, true))
                ->setCategory($this->getReference($faker->randomElement($categories)))
                ->setUser($this->getReference('USER' . $faker->numberBetween(1, 10)))
                ->setDuration($faker->numberBetween(2, 60));


            foreach ($faker->randomElements($ingredients, $faker->numberBetween(2,5)) as $ingredient){
                $recipe->addQuantity((new Quantity())
                    ->setQuantity($faker->numberBetween(1,250))
                    ->setUnit($faker->randomElement($units))
                    ->setIngredient($ingredient)
                );
            }

            $manager->persist($recipe);

        }
        // $product = new Product();

        $manager->flush();
    }


    public function getDependencies()
    {
        return [UserFixtures::class];

    }
}

<?php
namespace App\Controller\Admin\API;



use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    #[Route("/api/recipes", methods: ['GET'])]
    public function index(RecipeRepository $repository, Request $request, SerializerInterface $serializer)
    {
//       $recipes = $repository->paginationRecipes($request->query->getInt('page',1));
        $recipes = $repository->findAll();

        return $this->json($recipes, 200 , [], [
           'groups' => [ 'recipes.index']
       ]);
    }



    #[Route("/api/recipes/{id}", requirements: ['id'=> Requirement::DIGITS])]
    public function show(Recipe $recipe)
    {

        return $this->json($recipe, 200 , [], [
            'groups' => [ 'recipes.index', 'recipes.show']
        ]);
    }



    #[Route("/api/recipes", methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer)
    {

        $recipe = new Recipe();
        $recipe->setCreatedAt(new \DateTimeImmutable());
        $recipe->setUpdatedAt(new \DateTimeImmutable());

        dd($serializer->deserialize($request->getContent(), Recipe::class, 'json',[
            AbstractNormalizer::OBJECT_TO_POPULATE=>$recipe,
            'groups' => ['recipes.create']
        ]));

    }



}
<?php

namespace App\Controller\Admin;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\CategoryRepository;
use App\Repository\RecipeRepository;
use App\Security\Voter\RecipeVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route("/admin/recettes", name: "admin.recipe.")]
#[IsGranted('ROLE_ADMIN')]
class RecipeController extends AbstractController
{

//    #[Route('/', name: 'index')]
//    #[IsGranted(RecipeVoter::LIST)]
//    public function index(Request $request, RecipeRepository $repository, EntityManagerInterface $em,CategoryRepository $categoryRepository, Security $security ): Response
//    {
//
////        $recipes = $repository->findwithDurationLowerThan(10);
//
////        dd($repository->findTotalDuration());
//
//
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
//
//
//        $page = $request->query->getInt('page', 1);
//        $userId= $security->getUser()->getId();
//        $canListAll= $security->isGranted(RecipeVoter::LIST_ALL);
//        $limit = 5;
//        $recipes = $repository->paginationRecipes($page, $canListAll ? null: $userId);
//        $maxPage = ceil($recipes->getTotalItemCount()/$limit);
//
//
//
//
////        $em->remove($recipes[3]);
////        $em->flush();
//
////        $recipes[0]->setTitre('Pates boolognaise');
//
////        $recipe = new Recipe();
////        $recipe->setTitre('Barbe a papa')
////            ->setSlug("barbe-papa")
////            ->setContent('Mettez du sucre')
////            ->setDuration(2)
////            ->setCreatedAt(new \DateTimeImmutable())
////            ->setUpdatedAt(new \DateTimeImmutable());
////
////        $em->persist($recipe);
//
////            $em->flush();
//
//
//        return $this->render('admin/recipe/index.html.twig', [
//
//            'recipes' => $recipes,
//            'maxPage' => $maxPage,
//            'page'=>$page
//        ]);
//
//
//    }




    #[Route('/', name: 'index')]
    #[IsGranted(RecipeVoter::LIST)]
    public function index(Request $request, RecipeRepository $repository, Security $security): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $page = $request->query->getInt('page', 1);
        $searchTerm = $request->query->get('search', '');
        $userId = $security->getUser()->getId();
        $canListAll = $security->isGranted(RecipeVoter::LIST_ALL);
        $limit = 6;

        if ($searchTerm) {
            $recipes = $repository->searchRecipes($searchTerm, $page, $limit, $canListAll ? null : $userId);

            // Fallback to all recipes if no matches found
            if (count($recipes) === 0) {
                $recipes = $repository->paginationRecipes($page, $canListAll ? null : $userId);
            }
        } else {
            $recipes = $repository->paginationRecipes($page, $canListAll ? null : $userId);
        }

        $maxPage = ceil($recipes->getTotalItemCount() / $limit);

        return $this->render('admin/recipe/index.html.twig', [
            'recipes' => $recipes,
            'maxPage' => $maxPage,
            'page' => $page,
            'searchTerm' => $searchTerm,
        ]);
    }

    #[Route('/{slug}-{id}', name: 'show', methods: ['GET'], requirements: ['id' => '\d+', 'slug' => '[a-z0-9-]+'])]

    public function show(Request $request, string $slug, int $id, RecipeRepository $repository): Response
    {
        $recipe = $repository->find($id);

        if ($recipe->getSlug() != $slug) {
            return $this->redirectToRoute('admin.recipe.show', ['slug' => $recipe->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('admin/recipe/show.html.twig', [
            'recipe' => $recipe
        ]);
    }

    #[Route('/{id}' , name:'edit', methods: ['GET' , 'POST'], requirements: ['id' => Requirement::DIGITS] )]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function edit(Recipe $recipe, Request $request, EntityManagerInterface $em): Response{

        $form = $this->createForm(RecipeType::class, $recipe);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
//            $recipe->setUpdatedAt(new \DateTimeImmutable());
//            $file = $form->get('thumbnailFile')->getData();
//            $filename= $recipe->getId(). '.' . $file->getClientOriginalExtension();
//            $file->move($this->getParameter('kernel.project_dir') . '/public/recettes/images', $filename);
//            $recipe->setThumbmail($filename);
            $em->flush();
            $this->addFlash('success', 'La recette a bien ete modifiee');
            return $this->redirectToRoute("admin.recipe.index");
        }

        return $this->render('admin/recipe/edit.html.twig',[
            'recipe' => $recipe,
            'form' => $form
        ]);
    }
    #[Route('/create', name: 'create')]
    #[IsGranted(RecipeVoter::CREATE)]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
//            $recipe->setCreatedAt(new \DateTimeImmutable());
//            $recipe->setUpdatedAt(new \DateTimeImmutable());

            $em->persist($recipe);
            $em->flush();
            $this->addFlash('success', 'La recette a bien ete ajouter');
            return $this->redirectToRoute("admin.recipe.index");

        }
        return $this->render('admin/recipe/create.html.twig', [
            'form'=> $form
        ]);

    }


    #[Route('/{id}', name: 'delete', methods: ['DELETE'],requirements: ['id' => Requirement::DIGITS] )]
    #[IsGranted(RecipeVoter::EDIT, subject: 'recipe')]
    public function  remove(Recipe $recipe, EntityManagerInterface $em, Request $request)
    {

        $recipeId = $recipe->getId();
        $em->remove($recipe);
        $em->flush();

        if($request->getPreferredFormat() == TurboBundle::STREAM_FORMAT){
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('admin/recipe/delete.html.twig', [ 'recipeId'=>$recipeId]);
        }

         $this->addFlash('success', 'La recette a bien ete supprimer');
        return $this-> redirectToRoute('admin.recipe.index');
    }

}

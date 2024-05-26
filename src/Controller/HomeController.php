<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    function index(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
//        return new Response('Bonjour '. $request->query->get('name')  );



        return $this->render('home/index.html.twig');
    }
}

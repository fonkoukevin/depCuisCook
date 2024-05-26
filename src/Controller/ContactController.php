<?php

namespace App\Controller;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $data = new ContactDTO();

//
//
//        $data->name = 'John Doe';
//        $data->email = 'kevinfonkou09@gmail.com';
//        $data->message = 'Hello';

        $form = $this->createForm(ContactType::class, $data);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {

            $mail = (new TemplatedEmail())
                ->to($data->service)
                ->from($data->email)
                ->subject('Demande de contact')
                ->htmlTemplate('emails/contact.html.twig')
                ->context(['data' => $data]);
                $mailer->send($mail);
                $this->addFlash('success','le mail a ete envoyer avec seccess');

                return $this->redirectToRoute('contact');

            }catch (\Exception $e){
                $this->addFlash('danger',"Impossible d'envoyer le mail");
            }

        }
        return $this->render('contact/contact.html.twig', [
            'form'=>$form,
        ]);
    }


}





<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Repository\PostRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findLastThreePosts();

        return $this->render('home/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/contact", name="home_contact", methods={"GET", "POST"})
     */
    public function contact(Request $request, MailerInterface $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $formData = $contactForm->getData();
            $mail     = (new TemplatedEmail())
                ->from('contact@monsupersiite.com')
                ->to($formData['mail'])
                ->subject($formData['subject'])
                ->htmlTemplate('mail/contact.html.twig')
                ->context([
                    'data' => $formData
                ])
            ;

            $mailer->send($mail);

            $this->addFlash('success', 'Votre demande a bien été envoyée !');
        }

        return $this->render('home/contact.html.twig', [
            'contact_form' => $contactForm->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{post}/show", name="post_show", methods={"GET"}, requirements={"post": "\d+"})
     */
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/add", name="post_add", methods={"GET", "POST"})
     */
    public function add(Request $request, PostRepository $postRepository): Response
    {
        $post     = new Post();
        $postForm = $this->createForm(PostType::class, $post);

        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $postRepository->add($post, true);

            $this->addFlash('success', 'Votre article a bien été enregistré !');

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/add.html.twig', [
            'post_form' => $postForm->createView()
        ]);
    }

    /**
     * @Route("/{post}/update", name="post_update", methods={"GET", "POST"}, requirements={"post": "\d+"})
     */
    public function update(Post $post, Request $request, PostRepository $postRepository): Response
    {
        $postForm = $this->createForm(PostType::class, $post);

        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $postRepository->add($post, true);

            $this->addFlash('success', 'Votre article a bien été mis à jour !');

            return $this->redirectToRoute('post_show', ['post' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/update.html.twig', [
            'post_form' => $postForm->createView()
        ]);
    }

    /**
     * @Route("/{post}/delete", name="post_delete", methods={"GET"}, requirements={"post": "\d+"})
     */
    public function delete(Post $post, PostRepository $postRepository): Response
    {
        $postRepository->remove($post, true);

        $this->addFlash('warning', 'L\'article a été supprimé !');

        return $this->redirectToRoute('post_index');
    }
}

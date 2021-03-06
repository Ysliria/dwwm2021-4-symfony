<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/post", name="post_")
 */
class PostController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{post}/show", name="show", methods={"GET", "POST"}, requirements={"post": "\d+"})
     */
    public function show(Post $post, Request $request, CommentRepository $commentRepository): Response
    {
        $comment     = new Comment();
        $commentForm = $this->createForm(CommentType::class, $comment);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment
                ->setAuthor($this->getUser())
                ->setPost($post)
                ->setCreatedAt(new \DateTimeImmutable('now'));

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Votre commentaire a bien été enregistré !');

            return $this->redirectToRoute('post_show', ['post' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/show.html.twig', [
            'post'         => $post,
            'comment_form' => $commentForm->createView()
        ]);
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function add(Request $request, PostRepository $postRepository): Response
    {
        $post     = new Post();
        $postForm = $this->createForm(PostType::class, $post);

        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            $slugger = new AsciiSlugger();

            $post->setSlug(strtolower($slugger->slug($post->getTitle())));
            $post->setCreatedAt(new \DateTimeImmutable('now'));
            $post->setAuthor($this->getUser());

            $postRepository->add($post, true);

            $this->addFlash('success', 'Votre article a bien été enregistré !');

            return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('post/add.html.twig', [
            'post_form' => $postForm->createView()
        ]);
    }

    /**
     * @Route("/{post}/update", name="update", methods={"GET", "POST"}, requirements={"post": "\d+"})
     * @IsGranted("ROLE_ADMIN")
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
     * @Route("/{post}/delete", name="delete", methods={"GET"}, requirements={"post": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Post $post, PostRepository $postRepository): Response
    {
        $postRepository->remove($post, true);

        $this->addFlash('warning', 'L\'article a été supprimé !');

        return $this->redirectToRoute('post_index', [], Response::HTTP_SEE_OTHER);
    }
}

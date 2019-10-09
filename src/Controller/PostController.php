<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Form\PostType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("posts/{page}", name="post_index", defaults={"page": 1}, requirements={"page": "\d+"}, methods={"GET"})
     */
    public function index($page, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $postRepository->getPostsQueryBuilder(),
            $page,
            3
        );

        return $this->render('post.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("post/new", name="post_new", methods={"GET","POST"})
     * @IsGranted("ROLE_POSTER")
     */
    public function new(Request $request): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post->setPreview('img/blog/1.jpg');
            $post->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/new.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("post/{slug}", name="post_show", requirements={"post": "[a-z\-_\d]+"}, methods={"GET"})
     */
    public function show(Post $post, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->getCommentsByPostWithUsers($post);

        $form = $this->createForm(CommentType::class);

        return $this->render('post_single.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'comment_form' => $form->createView()
        ]);
    }

    /**
     * @Route("post/{id}/edit", name="post_edit", requirements={"post": "\d+"}, methods={"GET","POST"})
     * @IsGranted("EDIT", subject="post")
     */
    public function edit(Request $request, Post $post): Response
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("post/{id}", name="post_delete", requirements={"post": "\d+"}, methods={"DELETE"})
     * @IsGranted("DELETE", subject="post")
     */
    public function delete(Request $request, Post $post): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_index');
    }
}

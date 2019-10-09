<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/new/{id}", name="comment_new", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, Post $post, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setPost($post);
            $comment->setUser($this->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post_show', [
                'slug' => $post->getSlug()
            ]);
        }

        $comments = $commentRepository->getCommentsByPostWithUsers($post);

        return $this->render('post_single.html.twig', [
            'post' => $post,
            'comments' => $comments,
            'comment_form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="comment_edit", methods={"GET","POST"})
     * @IsGranted("EDIT", subject="comment")
     */
    public function edit(Request $request, Comment $comment): Response
    {
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_index');
        }

        return $this->render('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="comment_delete", methods={"DELETE"})
     * @IsGranted("DELETE", subject="comment")
     */
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('post_show', [
            'slug' => $comment->getPost()->getSlug()
        ]);
    }
}

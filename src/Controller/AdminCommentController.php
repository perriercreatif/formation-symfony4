<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $repo, $page, PaginationService $pagination){
        $pagination->setEntityClass(Comment::class)
            ->setLimit(5)
            ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/edit" , name="admin_comment_edit")
     * @param Comment $comment
     * @return Response
     */
    public function edit(Comment $comment, ObjectManager $manager, Request $request){
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaires <strong>{$comment->getId()}</strong> a bien été enregistrées !"
            );
        }
        return $this->render('admin/comment/edit.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }

    /**
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager){
            $manager->remove($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le commentaire <strong>{$comment->getId()}</strong> a bien été supprimée !"
            );

        return $this->redirectToRoute("admin_comment_index");
    }
}

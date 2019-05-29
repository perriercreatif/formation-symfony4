<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController{
    /**
     * @Route("/admin/users{page<\d+>?1}", name="admin_user_index")
     */
    public function index(UserRepository $repo, $page, PaginationService $pagination){
        $pagination->setEntityClass(User::class)
            ->setPage($page);

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/users/{id}/edit" , name="admin_user_edit")
     * @param User $user
     * @return Response
     */
    public function edit(User $user, ObjectManager $manager, Request $request){
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "L'utilisateur <strong>{$user->getId()}</strong> a bien été enregistrées !"
            );
        }
        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/admin/users/{id}/delete", name="admin_user_delete")
     * @param User $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(User $user, ObjectManager $manager){
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'utilisateur <strong>{$user->getId()}</strong> a bien été supprimée !"
        );

        return $this->redirectToRoute("admin_user_index");
    }
}

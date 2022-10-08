<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\SearchUserType;
use App\Form\UserType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/utilisateurs', name: 'admin_users')]
    public function index(
        UserRepository $userRepository,
        Request $request
        ): Response
    {

        $searchForm = $this->createForm(SearchUserType::class);
        $searchForm->handleRequest($request);

        $users = $userRepository->findAll();

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchType = $searchForm->get('searchType')->getData();
            $query = $searchForm->get('query')->getData();

            if ($searchType === 'id') {
                $users = $userRepository->findUsersById($query);
            } elseif ($searchType === 'email') {
                $users = $userRepository->findUsersByEmail($query);
            } 
        }

        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
            'form'  => $searchForm->createView()
        ]);
    }

    #[Route('/admin/utilisateur/{id}', name: 'admin_user')]
    public function show(
        User $user,
        OrderRepository $orderRepository
        ): Response
    {
        $orders = $orderRepository->findByUserDesc($user);

        return $this->render('admin/users/show.html.twig', [
            'user'   => $user,
            'orders' => $orders
        ]);
    }

    #[Route('admin/utilisateur/{id}/modifier', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
    User $user,
    UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user, true);

            $this->addFlash('success', 'Ustilisateur modifié');

            return $this->redirectToRoute('admin_user', [
                'id' => $user->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/utilisateur/supprimer/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request,
    User $user,
    UserRepository $userRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user, true);
        }

        $this->addFlash('success', 'utilisateur supprimé');

        return $this->redirectToRoute('admin_users', [], Response::HTTP_SEE_OTHER);
    }
}

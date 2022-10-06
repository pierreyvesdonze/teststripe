<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $em
    ) {
    }
    #[Route('/{id}', name: 'user_account', methods: ['GET'])]
    public function show(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userRepository->add($user, true);

            $this->addFlash('success', 'Vos informations ont été modifiées avec succès !');

            return $this->redirectToRoute('user_account', [
                'id' => $user->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/supprimer/{id}', name: 'user_disable')]
    public function disable(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $userAddress = $user->getAddress();
        $this->em->remove($userAddress);

        $userOrders = $user->getOrders();
        foreach ($userOrders as $order) {
            $order->setUser(null);
        }

        $this->em->remove($user);        
        $this->em->flush();

        $this->addFlash('success', 'Votre compte a bien été supprimé !');
        return $this->redirectToRoute('main');
    }
}

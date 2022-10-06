<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/adresse')]
class AddressController extends AbstractController
{
    public function __construct(
        private AddressRepository $addressRepository,
        private EntityManagerInterface $em
    )
    {
        
    }
    #[Route('s', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $this->addressRepository->findAll(),
        ]);
    }

    #[Route('/ajouter/adresse', name: 'address_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        $address = new Address();
        $address->setUser($this->getUser());
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->add($address, true);

            $userId = (string)$user->getId();

            if (
                $user->getCart() == true &&
                $user->getCart()->isValid() == true &&
                count($user->getCart()->getCartlines()) > 0
                ) {
                return $this->redirectToRoute('show_cart', [
                    'id' => $userId
                ]);
            }
            return $this->redirectToRoute('user_account', [
                'id' => $user->getId()
            ]);
        }

        return $this->renderForm('address/new.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/voir/{id}', name: 'address_show', methods: ['GET'])]
    public function show(Address $address): Response
    {
        return $this->render('address/show.html.twig', [
            'address' => $address,
        ]);
    }

    #[Route('/{id}/modifier', name: 'address_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Address $address): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addressRepository->add($address, true);

            return $this->redirectToRoute('user_account', [
                'id' => $this->getUser()->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'address_delete')]
    public function delete(
        Address $address
        ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $this->em->flush();

        $this->addFlash('success', 'Cette adresse a bien été supprimée.');
    
        return $this->redirectToRoute('user_account', [
            'id' => $this->getUser()->getId()
        ], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/adresse/session", name="set_address_session", methods={"GET","POST"}, options={"expose"=true})
     */
    public function setAddressSession(
        Request $request
    ): JsonResponse {

        $address = json_decode($request->getContent());
        $session = $request->getSession();

        $session->set('address', $address);

        return new JsonResponse($address);
    }
}

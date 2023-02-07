<?php

namespace App\Controller\Admin;

use App\Entity\Discount;
use App\Form\DiscountType;
use App\Repository\CartRepository;
use App\Repository\DiscountRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/discount')]
class AdminDiscountController extends AbstractController
{
    #[Route('/', name: 'admin_discount_index', methods: ['GET'])]
    public function index(DiscountRepository $discountRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('admin/discount/index.html.twig', [
            'discounts' => $discountRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_discount_new', methods: ['GET', 'POST'])]
    public function new(Request $request, DiscountRepository $discountRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discountRepository->save($discount, true);


            $this->addFlash('success', 'Code promo ajouté !');

            return $this->redirectToRoute('admin_discount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/discount/new.html.twig', [
            'discount' => $discount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_discount_show', methods: ['GET'])]
    public function show(Discount $discount): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        return $this->render('admin/discount/show.html.twig', [
            'discount' => $discount,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_discount_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Discount $discount,
        DiscountRepository $discountRepository,
        CartRepository $cartRepository
        ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discountRepository->save($discount, true);

            if(false == $form->get('isActiv')->getData()) {
                $carts = $discount->getCarts();
                foreach($carts as $cart) {
                    $cart->setDiscount(null);
                    $cartRepository->save($cart, true);
                }
            }

            $this->addFlash('success', 'Code promo modifié !');

            return $this->redirectToRoute('admin_discount_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/discount/edit.html.twig', [
            'discount' => $discount,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_discount_delete', methods: ['POST'])]
    public function delete(Request $request, Discount $discount, DiscountRepository $discountRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        if ($this->isCsrfTokenValid('delete' . $discount->getId(), $request->request->get('_token'))) {
            $discountRepository->remove($discount, true);
        }

        $this->addFlash('success', 'Code promo supprimé !');

        return $this->redirectToRoute('admin_discount_index', [], Response::HTTP_SEE_OTHER);
    }
}

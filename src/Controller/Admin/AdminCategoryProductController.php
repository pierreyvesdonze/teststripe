<?php

namespace App\Controller\Admin;

use App\Entity\CategoryProduct;
use App\Form\CategoryProductType;
use App\Repository\CategoryProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/category')]
class AdminCategoryProductController extends AbstractController
{
    #[Route('/index', name: 'admin_categories', methods: ['GET'])]
    public function index(CategoryProductRepository $categoryProductRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/categories/index.html.twig', [
            'category_products' => $categoryProductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_create_category', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryProductRepository $categoryProductRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $categoryProduct = new CategoryProduct();
        $form = $this->createForm(CategoryProductType::class, $categoryProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryProductRepository->add($categoryProduct, true);

            $this->addFlash('success', 'Nouvelle catégorie créée');

            return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/categories/new.html.twig', [
            'category_product' => $categoryProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin_category_show', methods: ['GET'])]
    public function show(CategoryProduct $categoryProduct): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/categories/show.html.twig', [
            'category' => $categoryProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryProduct $categoryProduct, CategoryProductRepository $categoryProductRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(CategoryProductType::class, $categoryProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categoryProductRepository->add($categoryProduct, true);

            $this->addFlash('success', 'Catégorie modifiée');

            return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/categories/edit.html.twig', [
            'category' => $categoryProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, CategoryProduct $categoryProduct, CategoryProductRepository $categoryProductRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$categoryProduct->getId(), $request->request->get('_token'))) {
            $categoryProductRepository->remove($categoryProduct, true);
        }

        $this->addFlash('success', 'Catégorie supprimée');

        return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
    }
}

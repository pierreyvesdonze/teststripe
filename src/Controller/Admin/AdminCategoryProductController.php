<?php

namespace App\Controller\Admin;

use App\Entity\CategoryProduct;
use App\Form\CategoryProductType;
use App\Repository\CategoryProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin/category')]
class AdminCategoryProductController extends AbstractController
{
    const MAX_CREATE_CATEGORY = 5;

    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/index', name: 'admin_categories', methods: ['GET'])]
    public function index(CategoryProductRepository $categoryProductRepository): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'category_products' => $categoryProductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_create_category', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryProductRepository $categoryProductRepository): Response
    {
        $totalCategories = count($categoryProductRepository->findAll());
        if ($totalCategories === self::MAX_CREATE_CATEGORY) {
            return $this->redirectToRoute('admin_categories');
        }

        $categoryProduct = new CategoryProduct();
        $form            = $this->createForm(CategoryProductType::class, $categoryProduct);
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
        return $this->render('admin/categories/show.html.twig', [
            'category' => $categoryProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CategoryProduct $categoryProduct, CategoryProductRepository $categoryProductRepository): Response
    {
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

    #[Route('/{id}/delete', name: 'admin_category_delete')]
    public function delete(CategoryProduct $categoryProduct): Response
    {
        $this->em->remove($categoryProduct);
        $this->em->flush();

        $this->addFlash('success', 'Catégorie supprimée');

        return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ProductRepository;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/product')]
class AdminProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('s', name: 'admin_products', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ProductRepository $productRepository,
        ImageManager $imageManager
         ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo = $form->get('image')->getData();

            if ($photo) {
                $photoFileName = $imageManager->upload($photo);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }
      
            $productRepository->add($product, true);

            $this->addFlash('success', 'Nouveau produit créé');

            return $this->redirectToRoute('admin_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/show/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->add($product, true);

            $this->addFlash('success', 'Produit modifié');

            return $this->redirectToRoute('app_product_show', [
                'id' => $product->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        ProductRepository $productRepository,
        ImageManager $imageManager
        ): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
            $imageManager->deleteImage($product->getImage());
        }

        $this->addFlash('success', 'Produit supprimé');

        return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
    }
}

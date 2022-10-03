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

#[Route('/admin/produit')]
class AdminProductController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('s', name: 'admin_products', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('admin/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/nouveau', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ProductRepository $productRepository,
        ImageManager $imageManager
         ): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image1 = $form->get('image')->getData();
            $image2 = $form->get('image2')->getData();
            $image3 = $form->get('image3')->getData();
            $image4 = $form->get('image4')->getData();
            $image5 = $form->get('image5')->getData();

            if ($image1) {
                $photoFileName = $imageManager->upload($image1);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            if ($image2) {
                $photoFileName = $imageManager->upload($image2);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            if ($image3) {
                $photoFileName = $imageManager->upload($image3);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            if ($image4) {
                $photoFileName = $imageManager->upload($image4);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            if ($image5) {
                $photoFileName = $imageManager->upload($image5);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            // If no image, set default image
            if (null == $form->get('image')->getData()) {
                $product->setImage('assets/images/noimage.png');
            }
      
            $productRepository->add($product, true);

            $this->addFlash('success', 'Nouveau produit créé !');

            return $this->redirectToRoute('admin_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/voir/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin/product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Product $product,
        ProductRepository $productRepository,
        ImageManager $imageManager
        ): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $image1 = $form->get('image')->getData();
            $image2 = $form->get('image2')->getData();
            $image3 = $form->get('image3')->getData();
            $image4 = $form->get('image4')->getData();
            $image5 = $form->get('image5')->getData();

            if ($image1) {
                $photoFileName = $imageManager->upload($image1);
                $imageManager->resize($photoFileName);
                $product->setImage($photoFileName);
            }

            if ($image2) {
                $photoFileName = $imageManager->upload($image2);
                $imageManager->resize($photoFileName);
                $product->setImage2($photoFileName);
            }

            if ($image3) {
                $photoFileName = $imageManager->upload($image3);
                $imageManager->resize($photoFileName);
                $product->setImage3($photoFileName);
            }

            if ($image4) {
                $photoFileName = $imageManager->upload($image4);
                $imageManager->resize($photoFileName);
                $product->setImage4($photoFileName);
            }

            if ($image5) {
                $photoFileName = $imageManager->upload($image5);
                $imageManager->resize($photoFileName);
                $product->setImage5($photoFileName);
            }

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

    #[Route('/supprimer/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Product $product,
        ProductRepository $productRepository,
        ImageManager $imageManager
        ): Response
    {       
        if ($this->isCsrfTokenValid('delete'.$product->getId(), 
        $request->request->get('_token'))) {

            $imageManager->deleteImage($product->getImage());
            $imageManager->deleteImage($product->getImage2());
            $imageManager->deleteImage($product->getImage3());
            $imageManager->deleteImage($product->getImage4());
            $imageManager->deleteImage($product->getImage5());
            $productRepository->remove($product, true);
        }

        $this->addFlash('success', 'Produit supprimé');

        return $this->redirectToRoute('products', [], Response::HTTP_SEE_OTHER);
    }
}

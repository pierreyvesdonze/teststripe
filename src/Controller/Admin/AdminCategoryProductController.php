<?php

namespace App\Controller\Admin;

use App\Entity\CategoryProduct;
use App\Form\CategoryProductType;
use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use App\Service\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/admin/category')]
class AdminCategoryProductController extends AbstractController
{
    const MAX_CREATE_CATEGORY = 5;

    public function __construct(
        private EntityManagerInterface $em,
        private ImageManager $imageManager,
        private CategoryProductRepository $categoryProductRepository
        )
    {
    }

    #[Route('/index', name: 'admin_categories', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'category_products' => $this->categoryProductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_create_category', methods: ['GET', 'POST'])]
    public function new(
        Request $request
        ): Response
    {
        $totalCategories = count($this->categoryProductRepository->findAll());
        if ($totalCategories === self::MAX_CREATE_CATEGORY) {
            $this->addFlash('error', 'Vous ne pouvez pas ajouter plus de '. self::MAX_CREATE_CATEGORY  . ' catégories');

            return $this->redirectToRoute('admin_categories');
        }

        $categoryProduct = new CategoryProduct();
        $form            = $this->createForm(CategoryProductType::class, $categoryProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Manage Banner
            if ($form->get('hasbanner')->getData() == 'Non') {
                $categoryProduct->setBanner(null);
            }
            if ($form->get('banner')->getData() != null) {
                $banner     = $form->get('banner')->getData();
                $bannerName = $this->imageManager->upload($banner, 'banner');
                $categoryProduct->setBanner($bannerName);
            }

            // Active or not Category on homepage
            if ($form->get('onHomepage')->getData() === 'Oui') {
                $categoryProduct->setOnHomepage(true);
            } else {
                $categoryProduct->setOnHomepage(false);
            }

            $this->categoryProductRepository->add($categoryProduct, true);

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
    public function edit(Request $request,
    CategoryProduct $categoryProduct
    ): Response
    {
        $form = $this->createForm(CategoryProductType::class, $categoryProduct);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $banner = $form->get('banner')->getData();

            // Manage Banner
            if ($form->get('hasbanner')->getData() == 'Non') {
                $this->imageManager->deleteImage($categoryProduct->getBanner());
                $categoryProduct->setBanner(null);
            }
            if ($form->get('banner')->getData() != null) {
                $bannerName = $this->imageManager->upload($banner, 'banner');
                $categoryProduct->setBanner($bannerName);
            }

            // Active or not Category on homepage
            if ($form->get('onHomepage')->getData() === 'Oui') {
                $categoryProduct->setOnHomepage(1);
            } else {
                $categoryProduct->setOnHomepage(0);
            }

            $this->categoryProductRepository->add($categoryProduct, true);

            $this->addFlash('success', 'Catégorie modifiée');

            return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/categories/edit.html.twig', [
            'category' => $categoryProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_category_delete')]
    public function delete(
        CategoryProduct $categoryProduct,
        ProductRepository $productRepository
        ): Response
    {
        $this->imageManager->deleteImage($categoryProduct->getBanner());

        $products = $productRepository->findBy([
            'categoryProduct' => $categoryProduct->getId()
        ]);

        foreach ($products as $product) {
            $product->setCategoryProduct(null);
        }


        $this->em->remove($categoryProduct);
        $this->em->flush();

        $this->addFlash('success', 'Catégorie supprimée');

        return $this->redirectToRoute('admin_categories', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/modifier/ordre", name="change_category_order", methods={"GET","POST"}, options={"expose"=true})
     */
    public function removeFromCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {

            $categId  = json_decode($request->getContent())->categId;
            $newOrder = (int)json_decode($request->getContent())->orderValue;

            $category = $this->categoryProductRepository->findOneBy([
                'id' => $categId
            ]);

            $category->setOrderHomepage($newOrder);

            $this->em->flush();
        }
        return new JsonResponse('ok');
    }
}

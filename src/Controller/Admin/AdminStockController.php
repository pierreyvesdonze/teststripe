<?php

namespace App\Controller\Admin;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminStockController extends AbstractController
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $em
    )
    {
        
    }
    #[Route('/admin/stock', name: 'admin_stock')]
    public function index(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('admin/stock/index.html.twig', [
            'products' => $products,
        ]);
    }

    /**
     * @Route("/admin/modifier/stock", name="change_stock_quantity", methods={"GET","POST"}, options={"expose"=true})
     */
    public function removeFromCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {

            $productId   = json_decode($request->getContent())->id;
            $newQuantity = json_decode($request->getContent())->quantity;
            
            /**
             * @var Product $product
             */
            $product = $this->productRepository->findOneBy([
                'id' => $productId
            ]);

            $product->setStock($newQuantity);

            $this->em->flush();            
        }
        return new JsonResponse('ok');
    }
}

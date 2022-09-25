<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $em,
        private ProductRepository $productRepository
        )
    {}

    /**
     * @Route("/cart/add", name="add_to_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function addToCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {

            $session = $this->requestStack->getSession();
            //$session->clear();

            $articleId = $request->getContent();

            if ($session->get('cartSessionId')) {
                $cart = $session->get('cart', []);
            } else {
                $this->createCart();
            }

            if (!empty($cart[$articleId])) {
                $cart[$articleId]++;
            } else {
                $cart[$articleId] = 1;
            }

            $session->set('cart', $cart);

            return new JsonResponse($cart);
        }
    }

    /**
     * @return Cart $cart (empty except sessionId)
     * Create cart both in db and session
     */
    public function createCart()
    {
        $cart = new Cart;
        $cart->setSessionId(uniqid());
        $cart->setIsValid(1);
        $cart->setUser($this->getUser());

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

     /**
     * @Route("/cart/validate", name="validate_session_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function validateCart(
        Request $request
        ): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse('false');
        }
        
        if ($request->isMethod('POST')) {
            $cartFromFront = json_decode($request->getContent());

            // Reset Cart
            if ($user->getCart()) {
                $this->em->remove($user->getCart());
            }

            // Create new one
            $cart = $this->createCart();
            $cart->setUser($user);

            foreach ($cartFromFront as $key => $value) {
                $testArray[] = $value;
                $product = $this->productRepository->findOneBy([
                    'id' => (int)$value
                ]);
                
                $newCartLine = new CartLine;
                $newCartLine->setProduct($product);
                $newCartLine->setCart($cart);

                $this->em->persist($newCartLine);
            }

            $this->em->persist($cart);
            $this->em->flush();
        }

        return new JsonResponse($testArray);
    }
}

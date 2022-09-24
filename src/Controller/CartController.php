<?php

namespace App\Controller;

use App\Entity\Cart;
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
        private EntityManagerInterface $em
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
     * @Route("/cart/session", name="get_session_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function getSessionCart(
        ProductRepository $productRepository,
        Request $request
        )
    {
        if ($request->isMethod('POST')) {
            $session = $this->requestStack->getSession();
            $cartArray = [];
            foreach ($session->get('cart') as $productId => $quantity) {
                $cartArray[
                    $productRepository->findOneBy([
                    'id' => $productId
                ])->getId()
                ] = $quantity;
            }
        }

        return new JsonResponse($cartArray);
    }

    /**
     * @return Cart $cart (empty except sessionId)
     * Create cart both in db and session
     */
    public function createCart()
    {
        $cart = new Cart;
        $cart->setSessionId(uniqid());

        $this->em->persist($cart);
        $this->em->flush();

        $session = $this->requestStack->getSession();
        $session->set('cart', []);
        $session->set('cartSessionId', $cart->getSessionId());

        return $cart;
    }

    /**
     * @return bool
     * @param Cart $cart
     */
    public function checkSessionCart(Cart $cart)
    {
        $session = $this->requestStack->getSession();

        $bool = false;

        $cart->getSessionId() == $session->get('cartSessionId') ? $bool = true : $bool = false;

        return $bool;
    }
}

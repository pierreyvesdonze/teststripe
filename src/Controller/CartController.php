<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\User;
use App\Repository\CartLineRepository;
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
        private ProductRepository $productRepository,
        private CartLineRepository $cartLineRepository
    ) {
    }

    /**
     * @Route("/panier/voir/{id}", name="show_cart", methods={"GET","POST"})
     */
    public function showCart(
        User $user
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $cart      = $user->getCart();
        $totalCart = 0;

        foreach ($cart->getCartLines() as $cartLine) {
            $totalCart += $cartLine->getProduct()->getPrice() * $cartLine->getQuantity();
        }

        return $this->render('cart/show.html.twig', [
            'cart'      => $cart,
            'totalCart' => $totalCart
        ]);
    }

    /**
     * @Route("/cart/remove", name="remove_from_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function removeFromCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {

            $cartlineId = json_decode($request->getContent());
            $cartline   = $this->cartLineRepository->findOneBy([
                'id' => $cartlineId
            ]);
        
            $this->em->remove($cartline);
            $this->em->flush();

            return new JsonResponse('Product removed');
        }
    }

    /**
     * @return Cart $cart (empty except sessionId)
     * Create cart both in db and session
     */
    public function createCart()
    {
        $cart = new Cart;
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
    ): JsonResponse {
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

            // For Response
            $cartArray = [];

            foreach ($cartFromFront as $key => $value) {

                $cartArray[] = $value;
                $product = $this->productRepository->findOneBy([
                    'id' => (int)$value->id
                ]);

                $newCartLine = new CartLine;
                $newCartLine->setProduct($product);
                $newCartLine->setQuantity($value->quantity);
                $newCartLine->setCart($cart);

                $this->em->persist($newCartLine);
            }

            $this->em->persist($cart);
            $this->em->flush();
        }

        return new JsonResponse($cartArray);
    }

    /**
     * @Route("/cart/update", name="update_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function updateCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {
            $cartLineId  = json_decode($request->getContent())->cartline;
            $addOrRemove = json_decode($request->getContent())->type;
            $cartLine    = $this->cartLineRepository->findOneBy([
                'id' => $cartLineId
            ]);

            if ($addOrRemove == 'add') {
                $cartLine->setQuantity($cartLine->getQuantity() + 1);
            } else {
                $cartLine->setQuantity($cartLine->getQuantity() - 1);
            }

            $this->em->flush();
        }
        return new JsonResponse('ok');
    }
}

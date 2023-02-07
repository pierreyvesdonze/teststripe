<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\User;
use App\Repository\CartLineRepository;
use App\Repository\ProductRepository;
use App\Service\DiscountManager;
use App\Service\StockManager;
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
     * @Route("/panier/voir/{id}", name="show_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function showCart(
        User $user,
        DiscountManager $discountManager,
        Request $request
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $cart              = $user->getCart();
        $totalCart         = 0;
        $discount          = 0;

        if (count($cart->getCartLines()) < 1) {
            return $this->redirectToRoute('main');
        }

        foreach ($cart->getCartLines() as $cartLine) {
            $totalCart += $cartLine->getProduct()->getPrice() * $cartLine->getQuantity();
        }

        // TODO : Manage Discount
        if ($request->isMethod('POST')) {
            $userInput = json_decode($request->getContent());
            $discount  = $discountManager->getDiscount($userInput);
            
            if (null !== $discount) {
                $cart->setDiscount($discount);
                $this->em->flush();
                $this->addFlash('success', 'Code promo appliqué !');
                
                return new JsonResponse('ok');
            } else {
                $this->addFlash('error', 'Code promo non trouvé');
                
                return new JsonResponse('Non ok');
            }
        }

        return $this->render('cart/show.html.twig', [
            'cart'              => $cart,
            'totalCart'         => $totalCart,
        ]);
    }

    /**
     * @Route("/cart/remove", name="remove_from_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function removeFromCart(
        Request $request
    ): JsonResponse {

        if ($request->isMethod('POST')) {

            $productId = json_decode($request->getContent());
            $cartline   = $this->cartLineRepository->findOneBy([
                'product' => $productId
            ]);

            if ($cartline) {
                $this->em->remove($cartline);
                $this->em->flush();
            }

            if (count($this->getUser()->getCart()->getCartLines()) === 0) {
                $this->getUser()->getCart()->setIsValid(0);
                $this->em->flush();
            }

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
        $cart->setIsValid(0);
        $cart->setUser($this->getUser());

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    /**
     * @Route("/cart/validate", name="validate_session_cart", methods={"GET","POST"}, options={"expose"=true})
     */
    public function validateCart(
        Request $request,
        CartLineRepository $cartLineRepository,
        StockManager $stockManager
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse('false');
        }

        if ($request->isMethod('POST')) {
            $cartFromFront = json_decode($request->getContent());

            if ($user->getCart()) {
                $cart = $user->getCart();

                // Clear Cart
                $cartLinesToRemove = $cartLineRepository->findAllByCartId($cart->getId());

                foreach ($cartLinesToRemove as $cartLine) {
                    $this->em->remove($cartLine);
                }
            } else {
                $cart = $this->createCart();
                $cart->setUser($user);
                $cart->setIsValid(1);
            }

            $this->em->flush();

            // For Response
            $cartArray = [];

            foreach ($cartFromFront as $key => $value) {

                $cartArray[] = $value;
                $product = $this->productRepository->findOneBy([
                    'id' => (int)$value->id
                ]);

                $newCartLine = new CartLine;
                $newCartLine->setProduct($product);

                // TODO Check availability in stock and return correct quantity to put in cart
                // $realQuantity = $stockManager->updateQuantityInCart($product->getStock(), $value->quantity);

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

        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

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

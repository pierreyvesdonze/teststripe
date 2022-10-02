<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\UserRate;
use App\Form\ProductRateType;
use App\Repository\UserRateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserRateController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/avis/ajouter/{id}', name: 'add_user_rate')]
    public function add(
        Product $product,
        Request $request
        ): Response
    {
        $userRate = new UserRate;
        $form = $this->createForm(ProductRateType::class, $userRate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { 
            $userRate->setProduct($product);
            $userRate->setCreatedAt(new \DateTimeImmutable('now'));
            $this->em->persist($userRate);
            $this->em->flush();

            $this->addFlash('success', 'Merci pour votre avis !');

            return $this->redirectToRoute('product_show', [
                'id' => $userRate->getProduct()->getId()
            ]);
        }

        return $this->render('user_rate/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\CategoryProductRepository;
use App\Repository\ProductRepository;
use App\Repository\StarringProductRepository;
use App\Repository\UserRateRepository;
use App\Service\StockManager;
use App\Service\UserRateManager;
use Symfony\Component\Mailer\MailerInterface;
use App\Form\ContactType;
use App\Repository\BannertopRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(
        CategoryProductRepository $categoryProductRepository,
        StarringProductRepository $starringProductRepository,
        UserRateRepository $userRateRepository,
        BannertopRepository $bannertopRepository,
        UserRateManager $userRateManager,
        StockManager $stockManager
    ): Response {
        $categories      = $categoryProductRepository->findAllOrderedForHomepage();
        $starringProduct = $starringProductRepository->findAll();
        $bannertop       = $bannertopRepository->findAll();

        /* Starring product */
        if (!$starringProduct) {
            $starringProduct = null;
        } else {
            $starringProduct = $starringProduct[0];
        }
        if ($starringProduct != null) {
            $userRates = $userRateRepository->findAllByProduct($starringProduct->getProduct());
        } else {
            $userRates = null;
        }

        // Calculate average of rates
        if ($userRates != null) {
            $totalAverageProduct = $userRateManager->calculAverage($userRates);
        } else {
            $totalAverageProduct = 0;
        }

        // Total products in stock
        $totalStock = $stockManager->totalProductsInStock();

        return $this->render('main/index.html.twig', [
            'categories'          => $categories,
            'starringProduct'     => $starringProduct,
            'userRates'           => $userRates,
            'totalAverageProduct' => $totalAverageProduct,
            'totalStock'          => $totalStock,
            'bannerTop'           => $bannertop[0]
        ]);
    }

    public function navbar(
        CategoryProductRepository $categoriesRepository
    ) {
        return $this->render('nav/nav.html.twig', [
            'categories' => $categoriesRepository->findAll(),
        ]);
    }

    #[Route('/rechercher', name: 'search_product')]
    public function searchProduct(
        Request $request,
        ProductRepository $productRepository
    ) {
        $response = null;

        if ($request->isMethod('POST')) {
            $query    = $request->request->get('search');
            $response = $productRepository->findProductsByKeyword($query);
        }
        return $this->render('search/search.result.html.twig', [
            'products' => $response
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contact(
        MailerInterface $mailer,
        Request $request
    ): Response {

        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        $email = $this->getParameter('app.mail');

        if ($form->isSubmitted() && $form->isValid()) {

            $sender = $form->get('email')->getData();
            $text = $form->get('text')->getData();

            $message = (new TemplatedEmail())
                ->from($sender)
                ->to(
                    $email,
                )
                ->subject('De la part de ' . $sender . ' !')
                ->htmlTemplate('email/contact.notification.html.twig')
                ->context([
                    'sender'  => $sender,
                    'text' => $text
                ]);

            $mailer->send($message);

            $this->addFlash('success', 'Votre email a bien été envoyé !');

            return $this->redirectToRoute('main');
        }
        return $this->render('email/contact.form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

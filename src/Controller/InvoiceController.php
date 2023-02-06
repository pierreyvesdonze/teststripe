<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\DiscountManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceController extends AbstractController
{
    #[Route('/facture/telecharger/{id}', name: 'invoice_download')]
    public function download(
        Order $order,
        DiscountManager $discountManager
        ): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $pdfOptions = new Options();
        $pdfOptions->set(['defaultFont', 'Arial', 'isRemoteEnabled' => true]);

        $dompdf = new Dompdf($pdfOptions);

        $total = $order->getPrice();

        $html = $this->renderView('invoice/download.pdf.html.twig', [
            'order' => $order,
            'total' => $total
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Facture-" . $order->getReference() . ".pdf", [
            "Attachment" => true
        ]);

        return $dompdf;
    }
}

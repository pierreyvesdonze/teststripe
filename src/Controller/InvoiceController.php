<?php

namespace App\Controller;

use App\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;

class InvoiceController extends AbstractController
{
    #[Route('/facture/telecharger/{id}', name: 'invoice_download')]
    public function download(Order $order): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        $pdfOptions = new Options();
        $pdfOptions->set(['defaultFont']);

        $dompdf = new Dompdf($pdfOptions);

        $total = 0;
        foreach ($order->getOrderLines() as $orderline) {
            $total += $orderline->getProduct()->getPrice() * $orderline->getQuantity();
        }

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

        $this->response->removeHeader('Content-Type');

        return new Response('ok');
    }
}

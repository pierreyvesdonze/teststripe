<?php

namespace App\Controller\Admin;

use App\Entity\Bannertop;
use App\Repository\BannertopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBannertopController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
        
    }

    #[Route('/admin/banner/top/switch/', name: 'switch_bannertop_state', options: ['expose' => true])]
    public function switch(
        Request $request,
        BannertopRepository $bannertopRepository
        ): JsonResponse
    {
        if ($request->isMethod('POST')) {
            $state     = json_decode($request->getContent());
            $bannerTop = $bannertopRepository->findAll();

            $state == true ? $bannerTop[0]->setIsActiv(true) : $bannerTop[0]->setIsActiv(false);

            $this->em->flush();
        }
        return new JsonResponse($state);
    }

    #[Route('/admin/banner/top/edit/', name: 'admin_edit_bannertop', options: ['expose' => true])]
    public function editBannertop(
        Request $request,
        BannertopRepository $bannertopRepository
    ): JsonResponse {

        if(!$this->getUser()) {
            return $this->redirectToRoute('login');
        }

        if ($request->isMethod('POST')) {
           $userInput = json_decode($request->getContent());
           $bannertop = $bannertopRepository->findAll();

           if (!$bannertop) {
            $bannertop = new Bannertop();
            $bannertop->setText($userInput);
            $bannertop->setIsActiv(false);
            $this->em->persist($bannertop);
        }

        $bannertop[0]->setText($userInput);
        $this->em->flush();
    }
        return new JsonResponse('ok');
    }
}

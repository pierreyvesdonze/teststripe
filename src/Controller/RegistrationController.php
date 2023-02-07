<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    private $privateKey;

    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer,
        private EntityManagerInterface $em
        )
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setDiscount(0);
            $user->setCreatedAt(new \DateTime('now'));
            $user->setIsActiv(true);
            $user->setIsVerified(false);

            $this->em->persist($user);
            $this->em->flush();

            $session = $request->getSession();
            $session->set('id', $user->getId());
    
            // Create Customer on Stripe
            $this->registerCustomerOnStripe($user);

            $signatureComponents = $this->verifyEmailHelper->generateSignature(
                'registration_confirmation_route',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            $email = new TemplatedEmail();
            $email->from($this->getParameter('app.mail'));
            $email->to($user->getEmail());
            $email->htmlTemplate('registration/confirmation_email.html.twig');
            $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);

            $this->mailer->send($email);

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/user/verify', name: 'registration_confirmation_route')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository
        
        ): Response
    {
        $id = $request->getSession()->get('id');

        if (null === $id) {
            return $this->redirectToRoute('login');
        }

        $user = $userRepository->findOneBy([
            'id' => $id
        ]);

        if (null === $user) {
            return $this->redirectToRoute('login');
        }

        // try {
        //     $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        // } catch (VerifyEmailExceptionInterface $e) {
        //     $this->addFlash('verify_email_error', $e->getReason());

        //     return $this->redirectToRoute('register');
        // }

        $user->setIsVerified(true);
        $this->em->flush();

        $this->addFlash('success', 'Votre adresse email a bien été vérifiée');

        $session = $request->getSession();

        $session->clear();

        return $this->redirectToRoute('login');
    }

    public function registerCustomerOnStripe(User $user)
    {
        $stripe = new \Stripe\StripeClient(
            $this->privateKey
        );
        $stripe->customers->create([
            'phone' => $user->getPhoneNumber(),
            'email' => $user->getEmail(),
            'name' => $user->getFirstname() . ' ' . $user->getLastname()
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
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
        private MailerInterface $mailer
        )
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_TEST'];
        } else {
            $this->privateKey = $_ENV['STRIPE_SECRET_KEY_LIVE'];
        }
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
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

            $entityManager->persist($user);
            $entityManager->flush();
    
            // Create Customer on Stripe
            $this->registerCustomerOnStripe($user);

            // $signatureComponents = $this->verifyEmailHelper->generateSignature(
            //     'registration_confirmation_route',
            //     $user->getId(),
            //     $user->getEmail()
            // );

            // $email = new TemplatedEmail();
            // $email->from('pyd3.14@gmail.com');
            // $email->to($user->getEmail());
            // $email->htmlTemplate('registration/confirmation_email.html.twig');
            // $email->context(['signedUrl' => $signatureComponents->getSignedUrl()]);

            // $this->mailer->send($email);

            return $this->redirectToRoute('login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/verify", name="registration_confirmation_route")
     */
    public function verifyUserEmail(
        Request $request

        ): Response
    {
        $user = $this->getUser();

        // Do not get the User's Id or Email Address from the Request object
        try {
            $this->verifyEmailHelper->validateEmailConfirmation($request->getUri(), $user->getId(), $user->getEmail());
        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('verify_email_error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $user->setIsVerified(true);

        $this->addFlash('success', 'Votre adresse email a bien été vérifiée');

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

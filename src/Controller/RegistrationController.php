<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $dateNow = new \DateTime('now');

        if ($form->isSubmitted() && $form->isValid()) {
          $name = $user->getName();
          $email = $user->getEmail();

            try {
              if ($entityManager->getRepository('App:User')->findOneBy(['name' => $name])){
                  throw new Exception();
              }
            } catch (Exception $exception) {
                $this->addFlash('verify_email_error', 'Ce Pseudo est déjà utilisé');
                return $this->redirectToRoute('register');
            }

            try {
                if ($entityManager->getRepository('App:User')->findOneBy(['email' => $email])){
                    throw new Exception();
                }
            } catch (Exception $exception) {
                $this->addFlash('verify_email_error', 'Ce mail est déjà utilisé');
                return $this->redirectToRoute('register');
            }

            $user->setSignInDate($dateNow);
            $user->setRoles(['ROLE_USER']);
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('v.orru93@gmail.com', 'Vincent de SnowTricks'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $this->addFlash('success', 'Vérifiez votre boite mail');


            return $this->redirectToRoute('home');
        }


        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $idUser = $request->get('id');

        if (null === $idUser) {
            return $this->redirectToRoute('register');
        }
        $user = $userRepository->find($idUser);

        if (null === $user) {
            return $this->redirectToRoute('register');
        }


        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('home');
        }

        $this->addFlash('success', 'Votre adresse mail est bien vérifiée');

        return $this->redirectToRoute('login');
    }
}

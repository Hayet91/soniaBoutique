<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="app_register")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response

    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

             // encode the plain password
        $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            // $user->setRoles(["ROLE_USER"]);

            // $user = $form->getData();


            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre compte est correctement creé, veuillez vous connecter"
            );

            //envoie un message de confirmation du compte bien crée
            $mail = new Mail();
            $vars = [
                'firstname' => $user->getFirstname(),
            ];
            $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(),'Bienvenue sur Sonia Boutique', "welcome.html", $vars);

            return $this->redirectToRoute('app_login');
        }

        //si le formulaire est soumis alors :
        //tu enregistre les data en bdd
        //tu envoie un message de confirmation du compte bien crée

        return $this->render('register/index.html.twig', [ 
            'registerForm' => $form->createView()
        ]);
    }
}



 // Envoie d'un email de confirmation d'inscription
 $mail = new Mail();
 $vars = [
     'firstname' => $user->getFirstname(),
 ];
 $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Bienvenue sur La Boutique Française", "welcome.html", $vars);


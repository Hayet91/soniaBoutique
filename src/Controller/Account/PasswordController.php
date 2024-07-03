<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Response;


class PasswordController extends AbstractController

{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

 /**
     * @Route("/compte/modifier-mot-de-passe", name="app_account_modify_pwd")
     */
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $notification = null;

       
        $user = $this->getUser();
        $form = $this->createForm( PasswordUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();
            if ($userPasswordHasher->isPasswordValid($user, $old_pwd)) {

                $new_pwd = $form->get('new_password')->getData();
                $password = $userPasswordHasher->hashPassword($user, $new_pwd);

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = "Votre mot de passe a bien été mis à jour.";
                    }else {
                        $notification = "Votre mot de passe actuel n'est pas le bon";
                    }
                 }
                return $this->render('account/password/index.html.twig', [
                'form' => $form->createView(),
                'notification' => $notification
                ]);
            }

}
?>


    
<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * 1ère étape du tunnel d'achat
     * choix de l'adresse de livraison et du transporteur
     */

    /**
     * @Route("/commande/livraison", name="app_order")
     */
    public function index(): Response
    {

        // $user = $this->getUser();

        // if (!$user) {
        //     return $this->redirectToRoute('app_login'); // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
        // }
       
       $addresses = $this->getUser()->getAddresses();

       if(count ($addresses) == 0 ) {
        return $this->redirectToRoute('app_account_address_form');
       }
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl("app_order_summary")
        ]);

        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }

     /**
     * 1ème étape du tunnel d'achat
     * Recap de la commande de l'utilisateur
     * Isertion en base de données
     * Préparation du payement vers strype
     */

    /**
     * @Route("/commande/recapitulatif", name="app_order_summary")
     */
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }
        $products = $cart->getCart();
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(), 
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //stocker les informations en BDD

            //Création de la chaine adresse
            $addressObj = $form->get('addresses')->getData();
            $address = $addressObj->getFirstname().' '.$addressObj->getLastname().'</br>';
            $address .= $addressObj->getAddress().'</br>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity().'</br>';
            $address .= $addressObj->getCountry().'</br>';
            $address .= $addressObj->getPhone();


            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail( $orderDetail);
            }
            $entityManager->persist($order);
            $entityManager->flush();
        }

        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'totalWt' => $cart->getTotalWt()
        ]);
    }
}

    
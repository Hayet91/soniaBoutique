<?php

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart 
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /*
    * add()
    * Fonction permettant l'ajout d'un produit au panier
    */
    public function add ($product) 
    {
    // apppeler la session de symfony
    $cart = $this->getCart();

    
    // ajouter un quantité +1 à mon produit
    if (isset($cart[$product->getId()])) {
        $cart[$product->getId()] = [
            'object' => $product,
            'qty' => $cart[$product->getId()] ['qty'] + 1
        ];
    }else {
        $cart[$product->getId()] = [
            'object' => $product,
            'qty' => 1
        ];
    }
    
    // Créer ma sésion cart
    $this->requestStack->getSession()->set('cart', $cart);
       
    }

    /*
    * decrease()
    * Fonction permettant la suppression d'une quantité d'un produit au panier
    */
    public function decrease($id)
    {
        $cart = $this->getCart();

        if ($cart[$id] ['qty'] > 1) {
            $cart[$id] ['qty'] = $cart[$id] ['qty'] -1;
        }else {
            unset($cart[$id]);
        }
    }

    /*
    * fullQuantity()
    * Fonction retournant le nombre total de produits au panier
    */
    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        if (!isset($cart)) {
            return  $quantity; // Ou gérer autrement l'erreur
        }

     

        foreach ($cart as $product) {
            $quantity = $quantity + $product ['qty'];
        }
        return $quantity;
    }

    /*
    * getTotalWt()
    * Fonction retournant le prix total des produits au panier
    */
    public function getTotalWt()
    {
        $cart = $this->getCart();
        $price = 0;

        if (!isset($cart)) {
            return $price; 
        }

       

        foreach ($cart as $product) {
            $price = $price + ($product ['object']->getPriceWt() * $product['qty']);
        }
        return $price;
    }

    /*
    * remove()
    * Fonction permettant de supprimer totalement le panier
    */
    public function remove()
    {
      return $this->requestStack->getSession()->remove('cart');
    }

    /*
    * getCart()
    * Fonction retournant le panier
    */
    public function getCart() 
    {
        return $this->requestStack->getSession()->get('cart');
    }
}
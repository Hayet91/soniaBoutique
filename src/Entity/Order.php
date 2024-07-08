<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $CarrierName;

    /**
     * @ORM\Column(type="float")
     */
    private $CarrierPrice;

    /**
     * @ORM\Column(type="text")
     */
    private $delivery;

    /**
    * @ORM\OneToMany(targetEntity=OrderDetail::class, mappedBy="myOrder", cascade={"persist"})
    */
    private $orderDetails;


    /*
     * 1 : en attente de paiement
     * 2 : paiement validé
     * 3 : expédié
     */

    /**
     * @ORM\Column(type="integer")
     */
    private $state;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

    public function getTotalWt()
    {
        $totalTTC = 0;
        $products = $this->getOrderDetails();

        foreach ($products as $product){
            $coeff = 1 + ($product->getProductTva() / 100);
            $totalTTC += ($product->getProductPrice() * $coeff) * $product->getProductQuantity();
        }
        return $totalTTC + $this->getCarrierPrice();
    }

    public function getTotalTva()
    {
        $totalTva = 0;
        $products = $this->getOrderDetails();

        foreach ($products as $product){
            $coeff = $product->getProductTva() / 100;
            $totalTva += $product->getProductPrice() * $coeff;
        }
        
            return $totalTva; 
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->CarrierName;
    }

    public function setCarrierName(string $CarrierName): self
    {
        $this->CarrierName = $CarrierName;

        return $this;
    }

    public function getCarrierPrice(): ?float
    {
        return $this->CarrierPrice;
    }

    public function setCarrierPrice(float $CarrierPrice): self
    {
        $this->CarrierPrice = $CarrierPrice;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(string $delivery): self
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}

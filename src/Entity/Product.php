<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    private ?string $description_short = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image3 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image4 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image5 = null;

    #[ORM\ManyToMany(targetEntity: Order::class, mappedBy: 'product')]
    private Collection $orders;

    #[ORM\ManyToOne(inversedBy: 'product')]
    #[ORM\JoinColumn(nullable: true)]
    private ?CategoryProduct $categoryProduct = null;

    #[ORM\Column]
    private ?bool $isActiv = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartLine::class, orphanRemoval: true)]
    private Collection $cartLines;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: OrderLine::class, orphanRemoval: true)]
    private Collection $orderLines;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: UserRate::class, orphanRemoval: true)]
    private Collection $userRates;

    #[ORM\Column]
    private ?int $stock = null;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
        $this->cartLines = new ArrayCollection();
        $this->orderLines = new ArrayCollection();
        $this->userRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescriptionShort(): ?string
    {
        return $this->description_short;
    }

    public function setDescriptionShort(string $description_short): self
    {
        $this->description_short = $description_short;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }


    public function getImage2(): ?string
    {
        return $this->image2;
    }

    public function setImage2(?string $image2): self
    {
        $this->image2 = $image2;

        return $this;
    }

    public function getImage3(): ?string
    {
        return $this->image3;
    }

    public function setImage3(?string $image3): self
    {
        $this->image3 = $image3;

        return $this;
    }

    public function getImage4(): ?string
    {
        return $this->image4;
    }

    public function setImage4(?string $image4): self
    {
        $this->image4 = $image4;

        return $this;
    }

    public function getImage5(): ?string
    {
        return $this->image5;
    }

    public function setImage5(?string $image5): self
    {
        $this->image5 = $image5;

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->addProduct($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            $order->removeProduct($this);
        }

        return $this;
    }

    public function getCategoryProduct(): ?CategoryProduct
    {
        return $this->categoryProduct;
    }

    public function setCategoryProduct(?CategoryProduct $categoryProduct): self
    {
        $this->categoryProduct = $categoryProduct;

        return $this;
    }

    public function isIsActiv(): ?bool
    {
        return $this->isActiv;
    }

    public function setIsActiv(bool $isActiv): self
    {
        $this->isActiv = $isActiv;

        return $this;
    }

    /**
     * @return Collection<int, CartLine>
     */
    public function getCartLines(): Collection
    {
        return $this->cartLines;
    }

    public function addCartLine(CartLine $cartLine): self
    {
        if (!$this->cartLines->contains($cartLine)) {
            $this->cartLines->add($cartLine);
            $cartLine->setProduct($this);
        }

        return $this;
    }

    public function removeCartLine(CartLine $cartLine): self
    {
        if ($this->cartLines->removeElement($cartLine)) {
            // set the owning side to null (unless already changed)
            if ($cartLine->getProduct() === $this) {
                $cartLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines->add($orderLine);
            $orderLine->setProduct($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getProduct() === $this) {
                $orderLine->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserRate>
     */
    public function getUserRates(): Collection
    {
        return $this->userRates;
    }

    public function addUserRate(UserRate $userRate): self
    {
        if (!$this->userRates->contains($userRate)) {
            $this->userRates->add($userRate);
            $userRate->setProduct($this);
        }

        return $this;
    }

    public function removeUserRate(UserRate $userRate): self
    {
        if ($this->userRates->removeElement($userRate)) {
            // set the owning side to null (unless already changed)
            if ($userRate->getProduct() === $this) {
                $userRate->setProduct(null);
            }
        }

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\PagosRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=PagosRepository::class)
 */
class Pagos
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @Assert\NotNull
     * @Assert\Length(
     *      min=4,
     *      max=100,
     *      allowEmptyString = false
     * )
     * @ORM\Column(type="string", length=100)
     */
    protected $descripcion;

    /**
     * @Assert\NotNull
     * @Assert\Positive
     * @ORM\Column(type="float")
     */
    protected $valor;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $session;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $token;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $status = false;

    /**
     * @ORM\ManyToOne(targetEntity=Wallet::class, inversedBy="pagos")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $wallet;

    /**
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $created_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getValor(): ?int
    {
        return $this->valor;
    }

    public function setValor(int $valor): self
    {
        $this->valor = $valor;

        return $this;
    }

    public function getSession(): ?string
    {
        return $this->session;
    }

    public function setSession(string $session): self
    {
        $this->session = $session;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(?Wallet $wallet): self
    {
        $this->wallet = $wallet;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }
}

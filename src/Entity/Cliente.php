<?php

namespace App\Entity;

use App\Repository\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 * @UniqueEntity("email")
 * @UniqueEntity("documento")
 */
class Cliente
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
     * @ORM\Column(type="string", length=100, unique=true)
     */
    protected $documento;

    /**
     * @Assert\NotNull
     * @Assert\Length(
     *      min=2,
     *      max=100,
     *      allowEmptyString = false
     * )
     * @ORM\Column(type="string", length=100)
     */
    protected $nombres;

    /**
     * @Assert\NotNull
     * @Assert\Email
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $email;

    /**
     * @Assert\NotNull
     * @Assert\Regex("/^\d{11}$/")
     * @ORM\Column(type="string", length=20)
     */
    protected $celular;

    /**
     * @ORM\OneToOne(targetEntity=Wallet::class, mappedBy="cliente", cascade={"persist", "remove"})
     */
    protected $wallet;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;
        return $this;
    }

    public function getNombres(): ?string
    {
        return $this->nombres;
    }

    public function setNombres(string $nombres): self
    {
        $this->nombres = $nombres;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getCelular(): ?string
    {
        return $this->celular;
    }

    public function setCelular(string $celular): self
    {
        $this->celular = $celular;
        return $this;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): self
    {
        $this->wallet = $wallet;

        // set the owning side of the relation if necessary
        if ($wallet->getCliente() !== $this) {
            $wallet->setCliente($this);
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 */
class Wallet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="float")
     */
    protected $saldo = 0;

    /**
     * @ORM\OneToOne(targetEntity=Cliente::class, inversedBy="wallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    protected $cliente;

    /**
     * @ORM\OneToMany(targetEntity=Pagos::class, mappedBy="wallet")
     */
    private $pagos;

    public function __construct()
    {
        $this->pagos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSaldo(): ?float
    {
        return $this->saldo;
    }

    public function setSaldo(float $saldo): self
    {
        $this->saldo = $saldo;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function recargarSaldo(float $valor)
    {
        $this->saldo = $this->saldo+$valor;
    }

    public function descontarSaldo(float $valor)
    {
        $this->saldo = $this->saldo-$valor;
    }

    /**
     * @return Collection|Pagos[]
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(Pagos $pago): self
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos[] = $pago;
            $pago->setWallet($this);
        }

        return $this;
    }

    public function removePago(Pagos $pago): self
    {
        if ($this->pagos->contains($pago)) {
            $this->pagos->removeElement($pago);
            // set the owning side to null (unless already changed)
            if ($pago->getWallet() === $this) {
                $pago->setWallet(null);
            }
        }

        return $this;
    }
}

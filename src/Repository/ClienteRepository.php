<?php

namespace App\Repository;

use App\Entity\Pagos;
use App\Entity\Cliente;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[]    findAll()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends ServiceEntityRepository
{
    protected $session;
    protected $mailer;

    public function __construct(ManagerRegistry $registry, SessionInterface $session, MailerInterface $mailer)
    {
        $this->session = $session;
        $this->mailer = $mailer;
        parent::__construct($registry, Cliente::class);
    }

    public function save(Cliente $cliente)
    {
        $this->_em->persist($cliente);
        $this->_em->flush();
    }

    public function findOneByDocumentoAndCelular(string $documento, string $celular)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.documento = :documento AND c.celular = :celular')
            ->setParameter('documento', $documento)
            ->setParameter('celular', $celular)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function savePago(Cliente $cliente, Pagos $pago)
    {
        if (!$this->session->isStarted()) {
            $this->session->start();
        }

        $pago->setWallet($cliente->getWallet());
        $pago->setSession($this->session->getId());

        $this->_em->getConnection()->beginTransaction();
        try {
            $this->_em->persist($pago);
            $this->_em->flush();
            $this->sendEmail($cliente->getEmail(), $pago->getToken());
            $this->_em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->_em->getConnection()->rollBack();
            throw $e;
        }
    }


    protected function sendEmail(string $correo, string $token)
    {
        // Enviar correo
        $mensaje = sprintf(
            "Ingrese el siguiente token en la ventana de confirmacion de pago: %s",
            $token
        );
        $email = (new Email())
            ->from(new Address('wallet@example.com', 'Info'))
            ->to($correo)
            ->replyTo('no-reply@example.com')
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Wallet: Confirmacion de Pago')
            ->text($mensaje)
            ->html($mensaje);
        $this->mailer->send($email);
    }
}

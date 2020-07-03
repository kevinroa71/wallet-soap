<?php

namespace App\Service;

use App\Entity\Pagos;
use App\Entity\Wallet;
use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use App\Repository\PagosRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ClienteService
{
    protected $repo_client;
    protected $repo_pagos;
    protected $validator;
    protected $serializer;

    public function __construct(ClienteRepository $repo_client, ValidatorInterface $validator, SerializerInterface $serializer, PagosRepository $repo_pagos)
    {
        $this->repo_client = $repo_client;
        $this->validator  = $validator;
        $this->serializer = $serializer;
        $this->repo_pagos = $repo_pagos;
    }

    /**
     * Servicio para registrar clientes
     *
     * @soap
     * @param string $documento
     * @param string $nombres
     * @param string $email
     * @param string $celular
     * @return string
     */
    public function registroCliente($documento, $nombres, $email, $celular)
    {
        $cliente = new Cliente();
        $cliente->setDocumento($documento);
        $cliente->setNombres($nombres);
        $cliente->setEmail($email);
        $cliente->setCelular($celular);
        $cliente->setWallet(new Wallet());

        $errors = $this->validateEntity($cliente);
        if (count($errors) > 0) {
            return $this->serializeResponse(false, 400, $errors);
        }

        $this->repo_client->save($cliente);

        return $this->serializeResponse(true, 201, $cliente, [AbstractNormalizer::IGNORED_ATTRIBUTES => ['wallet']]);
    }

    /**
     * Servicio para recargar el saldo de la billetera
     *
     * @soap
     * @param string $documento
     * @param string $celular
     * @param float $valor
     * @return string
     */
    public function recargarBilletera($documento, $celular, $valor)
    {
        if ($valor < 0) {
            return $this->serializeResponse(false, 400, ["Value must be greater than 0"]);
        }

        /** @var Cliente $cliente */
        $cliente = $this->repo_client->findOneByDocumentoAndCelular($documento, $celular);

        if (!$cliente) {
            return $this->serializeResponse(false, 404, ["Wallet not found!"]);
        }

        $cliente->getWallet()->recargarSaldo($valor);
        $this->repo_client->save($cliente);

        return $this->serializeResponse(true, 200, $cliente->getWallet(), [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cliente', 'pagos']]);
    }

    /**
     * Servicio para consultar el saldo
     *
     * @soap
     * @param string $documento
     * @param string $celular
     * @return string
     */
    public function consultarSaldo($documento, $celular)
    {
        /** @var Cliente $cliente */
        $cliente = $this->repo_client->findOneByDocumentoAndCelular($documento, $celular);

        if (!$cliente) {
            return $this->serializeResponse(false, 404, ["Wallet not found!"]);
        }

        return $this->serializeResponse(true, 200, $cliente->getWallet(), [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cliente', 'pagos']]);
    }

    /**
     * Servicio para crear un pago por una compra
     *
     * @soap
     * @param string $documento
     * @param string $celular
     * @param float $valor
     * @param string $descripcion
     * @return string
     */
    public function pagar($documento, $celular, $valor, $descripcion)
    {
        /** @var Cliente $cliente */
        $cliente = $this->repo_client->findOneByDocumentoAndCelular($documento, $celular);

        if (!$cliente) {
            return $this->serializeResponse(false, 404, ["Wallet not found!"]);
        }

        $wallet = $cliente->getWallet();
        if (!$wallet or $wallet->getSaldo() < $valor) {
            return $this->serializeResponse(false, 400, ["The balance is not enough to pay!"]);
        }

        $pago = new Pagos();
        $pago->setValor($valor);
        $pago->setDescripcion($descripcion);
        $pago->setToken(bin2hex(openssl_random_pseudo_bytes(3)));

        $errors = $this->validateEntity($pago);
        if (count($errors) > 0) {
            return $this->serializeResponse(false, 400, $errors);
        }

        try {
            $this->repo_client->savePago($cliente, $pago);
            return $this->serializeResponse(true, 201, $pago, [
                AbstractNormalizer::IGNORED_ATTRIBUTES => ['wallet', 'token', 'status']
            ]);
        } catch (\Exception $e) {
            return $this->serializeResponse(false, 500, [$e->getMessage()]);
        }
    }

    /**
     * Servicio para confirmar un pago y descontar el saldo de la billetera
     *
     * @soap
     * @param string $idsession
     * @param string $token
     * @return string
     */
    public function confirmarPago($token, $session)
    {
        /** @var Pagos $pago */
        $pago = $this->repo_pagos->findOneByTokenAndSession($token, $session);

        if (!$pago) {
            return $this->serializeResponse(false, 400, ["Confirmation data is wrong!"]);
        }

        $diff = $pago->getCreatedAt()->diff(new \DateTime());
        if ($diff->i > 10) {
            return $this->serializeResponse(false, 400, ["Confirmation time expired!"]);
        }

        $wallet = $pago->getWallet();
        if ($pago->getValor() > $wallet->getSaldo()) {
            return $this->serializeResponse(false, 400, ["The balance is not enough to pay!"]);
        }

        $wallet->descontarSaldo($pago->getValor());
        $pago->setStatus(true);

        $this->repo_pagos->save($pago);

        return $this->serializeResponse(true, 200, ["msg" => "Payment was successful!"]);
    }


    protected function validateEntity($entity)
    {
        $errors = $this->validator->validate($entity);
        $mensajes = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $mensajes[] = $error->getPropertyPath()." is not valid. Reason: ".$error->getMessage();
            }
        }
        return $mensajes;
    }

    protected function serializeResponse(bool $ok, int $code, $data, array $context = [])
    {
        $return = [
            "ok" => $ok,
            "code" => $code
        ];

        if ($ok) {
            $return["data"] = $data;
        } else {
            $return["errors"] = $data;
        }

        return $this->serializer->serialize($return, 'json', $context);
    }
}

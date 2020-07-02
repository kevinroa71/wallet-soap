<?php

namespace App\Service;

use App\Entity\Wallet;
use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class ClienteService
{
    protected $repository;
    protected $validator;
    protected $serializer;

    public function __construct(ClienteRepository $repository, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
        $this->serializer  = $serializer;
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

        $errors = $this->validator->validate($cliente);

        if (count($errors) > 0) {
            $mensajes = [];
            foreach ($errors as $error) {
                $mensajes[] = $error->getPropertyPath()." is not valid. Reason: ".$error->getMessage();
            }

            return $this->serializeResponse(false, 400, $mensajes);
        }

        $this->repository->save($cliente);

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
        $cliente = $this->repository->findOneByDocumentoAndCelular($documento, $celular);

        if (!$cliente) {
            return $this->serializeResponse(false, 404, ["Wallet not found!"]);
        }

        $cliente->getWallet()->recargarSaldo($valor);
        $this->repository->save($cliente);

        return $this->serializeResponse(true, 200, $cliente->getWallet(), [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cliente']]);
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
        $cliente = $this->repository->findOneByDocumentoAndCelular($documento, $celular);

        if (!$cliente) {
            return $this->serializeResponse(false, 404, ["Wallet not found!"]);
        }

        return $this->serializeResponse(true, 200, $cliente->getWallet(), [AbstractNormalizer::IGNORED_ATTRIBUTES => ['cliente']]);
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

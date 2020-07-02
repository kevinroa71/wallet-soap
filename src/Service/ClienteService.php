<?php

namespace App\Service;

use App\Entity\Cliente;
use App\Repository\ClienteRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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

        $errors = $this->validator->validate($cliente);

        if (count($errors) > 0) {
            $mensajes = [];
            foreach ($errors as $error) {
                $mensajes[] = $error->getPropertyPath()." is not valid. Reason: ".$error->getMessage();
            }

            return $this->serializer->serialize(
                [
                    "ok" => false,
                    "code" => 400,
                    "errors" => $mensajes
                ],
                'json'
            );
        }

        $this->repository->save($cliente);

        return $this->serializer->serialize(
            [
                "ok" => true,
                "code" => 201,
                "data" => $cliente
            ],
            'json'
        );
    }
}

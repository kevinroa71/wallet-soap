# Wallet SOAP

## Instalacion de dependencias
```
composer install
```

### Configurar el archivo de entorno local
> project-dir/.env.local
```
DATABASE_URL="mysql://root@127.0.0.1:3306/wallet"
SOAP_HOST=http://localhost:8000
MAILER_DSN=gmail+smtp://USER:PASS@default
```

### Compilar el archivo WSDL
```
php project-dir/bin/console app:create-wsdl
```

### Configurar la Base de Datos
```
php project-dir/bin/console doctrine:database:create
php project-dir/bin/console doctrine:migrations:migrate
```

### Configurar el servidor
Ver [Configuracion de Referencia](https://symfony.com/doc/current/setup/web_server_configuration.html).

### Verificar que todo este en orden
> http://dominio/soap?wsdl
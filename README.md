# Sobre o projeto

Aaa

# Características

Aaa

## Changelog

Veja o [histórico de atualizações](docs/changelog.md).

## Requisitos

Este sistema foi projetado para rodar em servidores linux (Ubuntu e Debian).

-   Laravel 11
-   PHP 8.3
-   Apache ou Nginx
-   Banco de dados local (MariaDB mas pode ser qualquer um suportado pelo Laravel)
-   Git
-   Composer
-   Credenciais para senha única
-   Acesso ao replicado (visão Pessoa - VUps, Estrutura - VUes e Financeiro - VUfi)

Bibliotecas necessárias do php:

    apt install php-sybase php-mysql php-xml php-intl php-mbstring php-gd php-curl php-zip

Descomentar a linha extension=soap do php.ini    

## Atualização

Caso você já tenha instalado o sistema e aplique uma nova atualização, sempre deve rodar:

    composer install --no-dev
    php artisan migrate

Também deve observar no [changelog](docs/changelog.md) se tem alguma outra coisa a ser ajustada, por exemplo o arquivo .env

## Instalação

    cd /var/www/html
    git clone git@github.com:USPdev/solicitacoes-documentos
    cd solicitacoes-documentos
    composer install
    cp .env.example .env
    php artisan key:generate

Criar user e banco de dados (em mysql):

    sudo mysql
    create database solicitacoesdocumentos;
    create user 'solicitacoesdocumentos'@'%' identified by '<<password here>>';    # nunca utilizar @ dentro da senha, pois dá erro no servidor de produção ao acessar o banco
    grant all privileges on solicitacoesdocumentos.* to 'solicitacoesdocumentos'@'%';
    flush privileges;

#### ################################ ####
## Configuração em ambiente de produção ##
#### ################################ ####

### Configurar o cache

A biblioteca (https://github.com/uspdev/cache) usada no replicado utiliza o servidor memcached. Se você pretende utilizá-lo instale e configure ele:

    sudo apt install memcached
    sudo vim /etc/memcached.conf
        I = 5M
        -m 128

    /etc/init.d/memcached restart

### E-mail

Configurar a conta de e-mail para acesso menos seguro pois a conexão é via smtp.

### Configurar o apache ou nginx

Criar novo arquivo solicitacoes-documentos.conf em /etc/apache2/sites-available; nele, dentro da tag VirtualHost, o DocumentRoot deve apontar para /var/www/html/solicitacoes-documentos/public. E para que as rotas funcionem, adicionar, ainda dentro dessa tag, a seguinte configuração:

    <Directory /var/www/html/solicitacoes-documentos/public>
        AllowOverride All
    </Directory>

E, em seguida, executar:

    sudo a2enmod rewrite
    sudo service apache2 restart

No Apache é possivel utilizar a extensão MPM-ITK (http://mpm-itk.sesse.net/) que permite rodar seu _Servidor Virtual_ com usuário próprio. Isso facilita rodar o sistema como um usuário comum e não precisa ajustar as permissões da pasta `storage/`.

    sudo apt install libapache2-mpm-itk
    sudo a2enmod mpm_itk                        # habilita o módulo
    sudo service apache2 restart

Dentro do solicitacoes-documentos.conf, dentro da tag VirtualHost coloque:

    <IfModule mpm_itk_module>
        AssignUserId nome_do_usuario nome_do_grupo
    </IfModule>

### Configurar senha única

Cadastre uma nova URL no configurador de senha única utilizando o caminho `https://seu_app/callback`. Guarde o callback_id para colocar no arquivo `.env`.

### Edite o arquivo .env

Há várias opções que precisam ser ajustadas nesse arquivo. Faça com atenção para não deixar passar nada. O arquivo está todo documentado.

### Popular banco de dados

    php artisan migrate

Os setores e respectivos designados podem ser importados do Replicado. Para isso rode:

    php artisan db:seed --class=SetorReplicadoSeeder

Depois de importado faça uma conferência para não haver inconsistências.

### Instalar e configurar o Supervisor

Para as filas de envio de e-mail, o sistema precisa de um gerenciador que mantenha rodando o processo que monitora as filas. O recomendado é o **Supervisor**. No Ubuntu ou Debian instale com:

    sudo apt install supervisor

Modelo de arquivo de configuração. Como **`root`**, crie o arquivo `/etc/supervisor/conf.d/solicitacoes_-_documentos_queue_worker_default.conf` com o conteúdo abaixo:

    [program:solicitacoes_documentos_queue_worker_default]
    command=/usr/bin/php /var/www/html/solicitacoes-documentos/artisan queue:listen --queue=default --tries=3 --timeout=60
    process_num=1
    username=www-data
    numprocs=1
    process_name=%(process_num)s
    priority=999
    autostart=true
    autorestart=unexpected
    startretries=3
    stopsignal=QUIT
    stderr_logfile=/var/www/html/solicitacoes-documentos/storage/logs/solicitacoes_documentos_worker_default.log

Ajustes necessários:

    command=<ajuste o caminho da aplicação>
    username=<nome do usuário do processo do solicitacoes-documentos>
    stderr_logfile = <aplicacao>/storage/logs/<seu arquivo de log>

Reinicie o **Supervisor**

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl restart all

### Permissão de escrita na pasta 'storage' ao usuário do browser:

É necessária essa permissão, pois o site utiliza sessões, que são gravadas em storage/framework/sessions.
E se ligarmos o modo debug, o site também quer gravar em storage/logs.

    sudo chown -R www-data:www-data /var/www/html/solicitacoes-documentos/storage
    sudo chmod -R 755               /var/www/html/solicitacoes-documentos/storage
    sudo service apache2 restart

#### ################### ####
## Atualização em produção ##
#### ################### ####

Para receber as últimas atualizações do sistema rode:

    cd /var/www/html/solicitacoes-documentos
    git pull
    composer install --no-dev
    php artisan migrate

Para atualizar os pacotes utilizados pelo sistema (por exemplo, o laravel-usp-theme), rode:

    composer update

Caso tenha alguma atualização, não deixe de conferir o readme.md quanto a outras providências que podem ser necessárias.

#### ####################################### ####
## Configuração em ambiente de desenvolvimento ##
#### ####################################### ####

Ainda é preciso descrever melhor mas pode seguir as instruções para ambiente de produção com os ajustes necessários.

    php artisan migrate:fresh --seed

O senhaunica-fake pode não ser adequado pois o sistema coloca as pessoas nos respectivos setores com as informações da senha única.

Para subir o servidor

    php artisan serve

**CUIDADO**: você pode enviar e-mails indesejados para as pessoas.

Para enviar e-mails e executar jobs agendadas é necessário executar as tarefas na fila. Para isso, em outro terminal, execute:

    php artisan queue:listen

## Problemas e soluções

Ao rodar pela primeira vez com apache, as variáveis de ambiente relacionadas ao replicado não ficam disponíveis. Nesse caso é necessário restartar o apache.

https://www.php.net/manual/pt_BR/function.getenv.php#117301

Para limpar e recriar todo o DB, rode sempre que necessário:

    php artisan migrate:fresh --seed

## Histórico

-   ??/??/????
    -   versão 1.0

## Detalhamento técnico

Foram utilizados vários recursos do laravel que podem não ser muito trivial para todos.

-   O monitoramento de novas solicitações ou novas mensagens nas solicitações é feito usando _observers_ (https://laravel.com/docs/8.x/eloquent#observers)

# Como Baixar

## 1° Clonar repositório

Inicie na sua maquina linux com o comando:

> git clone https://github.com/7s3ven7/BeTalent-API_de_Pagamento.git

Depois renomeie o diretório:

> mv BeTalent-API_de_Pagamento php

retire os arquivos de config:

> cd php

> mv betalent.sql ../../

> mv docker-compose.yml ../../

> mv Dockerfile ../../

> mv nginx.conf ../../

Retorne para o diretório base

> cd ../../

> ls

Devera aparecer o diretório php e os arquivos antes movidos.

## 2° Inicie o Docker

Agora inicie seu docker com o docker-compose

> docker-compose up --build -d

## 3° Banco de Dados

Copie o betalent.sql para o containner db:

> docker cp betalent.sql server_db_1:./

Entre no bash do container db:

> docker exec -it server_db_1 bash:

use o betalent.sql para criar a base de dados:

> mysql -u root -p BeTalent < betalent.sql

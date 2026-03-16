# Como Baixar

<br>

## 1° Clonar repositório

Inicie na sua maquina linux com o comando:

```git clone https://github.com/7s3ven7/BeTalent-API_de_Pagamento.git```

Depois renomeie o diretório:

```mv BeTalent-API_de_Pagamento php```

retire os arquivos de config:

```cd php```

```mv betalent.sql ../../```

```mv docker-compose.yml ../../```

```mv Dockerfile ../../```

```mv nginx.conf ../../```

Retorne para o diretório base

```cd ../../```

```ls```

Devera aparecer o diretório php e os arquivos antes movidos.

<br>

## 2° Inicie o Docker

Agora inicie seu docker com o docker-compose

```docker-compose up --build -d```

<br>

## 3° Banco de Dados

Copie o betalent.sql para o containner db:

```docker cp betalent.sql server_db_1:./```

Entre no bash do container db:

```docker exec -it server_db_1 bash```

use o betalent.sql para criar a base de dados:

```mysql -u root -p BeTalent < betalent.sql```

<br>

<br>

# Dificuldades

- Docker

Nunca tinha usado docker antes então foi um pouco complicado usa-lo em um projeto tão 'complexo',
quanto este, estou acostumado a testar tecnologias isoladas, então mesclar bibliotecas,
banco de dadoss, nginx e php tudo ao mesmo tempo enquanto usava o docker como infraestruturo do projeto, me fez
sofrer um pouco.

- Organização de Tempo

Comecei com o mais difícil sendo o docker, o qual não tinha usado até o momento, e fiquei 1 dia inteiro para conseguir
criar meu ambiente, depois fiquei 4 dias na programação da api, e mais 1 dia no final para testar e organizar,
ainda não tenho certeza se esta tudo funcionando, porem fiz e testei o que consegui, no tempo que me foi dado, com as habilidades que tenho.

- Interpretação do Problema

Quando comecei a programar a API foi tranquilo, comecei com os CRUDs que seriam a parte mais facil, depois fiz o login e por fim as transações,
e na parte das transações fiquei empacato por 2 dias, até terminar, por conta de não conseguir interpretar o que foi requerido, não entendi no inicio se a transiction
é criada antes ou depois do cliente, se a transiction_productt era antes ou depois tambem, mas depois de bater por bastante tempo eu consegui cadatrar tudo corretamente, até onde testei.

# Observações

Não tinha usado até então nenhuma ORM e nem um validador dedicado, sempre criei minha próprias ferramentos como meio de aprendizado,
então meu código não ficou bom no quesito de usabilidade da ORM (Eloquent) e do validador de dados (respect/validation), mas eu dei o meu melhor com a
pesquisa que fiz. No final, o projeto inteiro foi um grande aprendizado onde apliquei diversas tecnologias que eu desconhecia, muito complicado, porem muito bom para minha melhora.

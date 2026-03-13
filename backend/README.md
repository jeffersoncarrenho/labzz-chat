# Labzz Realtime Chat API

API backend para uma plataforma de **chat em tempo real**, desenvolvida como parte do **desafio técnico Fullstack da Labzz**.

Este projeto demonstra a construção de um backend escalável para sistemas de comunicação em tempo real utilizando:

- API REST
- WebSocket para comunicação em tempo real
- Redis Pub/Sub
- Elasticsearch para busca textual
- Infraestrutura com Docker
- Autenticação JWT
- Documentação OpenAPI (Swagger)

---

# Arquitetura

O sistema foi projetado separando responsabilidades entre API, persistência de dados, eventos em tempo real e busca.

Cliente
│
▼
API REST (PHP)
│
├── MySQL → persistência de dados
├── Redis → barramento de eventos (pub/sub)
├── WebSocket → entrega de mensagens em tempo real
└── Elasticsearch → busca textual em mensagens

---

# Stack Tecnológica

Backend

- PHP 8
- MySQL
- Redis
- Elasticsearch
- Ratchet WebSocket

DevOps

- Docker
- Docker Compose

Documentação

- OpenAPI (Swagger)

---

# Funcionalidades

Autenticação

- Login com JWT
- Proteção de endpoints

Usuários

- Criação de usuário

Mensagens

- Envio de mensagens
- Histórico de mensagens
- Broadcast em tempo real via WebSocket

Busca

- Busca textual em mensagens usando Elasticsearch

Infraestrutura

- Redis pub/sub
- Containers Docker
- Documentação OpenAPI

---

# Estrutura do Projeto

backend
│
├── src
│ ├── Controllers
│ ├── Core
│ ├── Middleware
│ ├── Services
│ └── WebSocket
│
├── routes
│
├── public
│ └── index.php
│
├── database
│ └── schema.sql
│
├── docs
│ └── OpenApiSpec.php
│
└── websocket-server.php

docker-compose.yml

---

# Instalação e Execução

Clonar o repositório

git clone https://github.com/jeffersoncarrenho/labzz-chat.git

cd labzz-chat

Subir os containers

docker compose up --build

Serviços iniciados:

| Serviço       | Porta |
| ------------- | ----- |
| API           | 8000  |
| WebSocket     | 8081  |
| MySQL         | 3306  |
| Redis         | 6379  |
| Elasticsearch | 9200  |

---

# Documentação da API

A especificação OpenAPI está disponível em:

public/openapi.json

Essa documentação pode ser utilizada em ferramentas como **Swagger UI** ou **Postman**.

---

# Principais Endpoints

## Autenticação

### Login

POST `/login`

{
"email": "usuario@email.com
",
"password": "senha"
}

Resposta

{
"token": "JWT_TOKEN"
}

---

## Usuários

Criar usuário

POST `/users`

{
"name": "João Silva",
"email": "joao@email.com
",
"password": "senha123"
}

---

## Mensagens

Enviar mensagem

POST `/messages`

{
"conversation_id": 1,
"user_id": 1,
"message": "Olá Labzz"
}

---

Obter histórico de mensagens

GET `/messages`

Parâmetros de query

conversation_id
page
limit

Exemplo

GET /messages?conversation_id=1&page=1&limit=20

---

## Busca

Buscar mensagens

GET `/search`

/search?query=olá

---

# WebSocket

O servidor WebSocket roda em:

ws://localhost:8081

Exemplo de cliente:

const ws = new WebSocket("ws://localhost:8081")

ws.onmessage = (msg) => console.log(msg.data)

---

# Fluxo de Mensagens em Tempo Real

POST /messages
│
▼
MySQL (salva mensagem)
│
▼
Redis publica evento
│
▼
WebSocket transmite
│
▼
Clientes conectados recebem mensagem

---

# Consulta direta no Elasticsearch

Exemplo:

curl http://localhost:9200/messages/\_search

---

# Melhorias Futuras

- Indicador de digitação
- Reações em mensagens
- Edição de mensagens
- Paginação otimizada
- Testes automatizados
- Rate limiting

---

# Autor

Jefferson Luiz Lima

Email: jefferson.carrenho@gmail.com

---

# Licença

MIT License

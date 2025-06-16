
---

# 📦 Treko - Backend

Projeto desenvolvido com [Laravel](https://laravel.com/) e utilizando [Laravel Sail](https://laravel.com/docs/sail) para ambiente de desenvolvimento containerizado.

## ✨ Tecnologias Utilizadas

* [Laravel](https://laravel.com/)
* [Laravel Sail](https://laravel.com/docs/sail)
* [MySQL](https://www.mysql.com/) via Docker
* [PHP 8.3](https://www.php.net/)
* [Redis](https://redis.io/) (opcional)
* [Mailpit](https://github.com/axllent/mailpit) (opcional para testes de email)

## 🚀 Como Rodar o Projeto

### Pré-requisitos

* Docker
* Docker Compose (ou apenas Docker Desktop atualizado)

Repositório do frontend disponível em: [https://github.com/exp-criativa-2025/web-frontend](https://github.com/exp-criativa-2025/web-frontend)

### Instalação

Clone o repositório:

```bash
git clone https://github.com/exp-criativa-2025/treko-laravel.git
```

Acesse a pasta do projeto:

```bash
cd treko-laravel
```

Copie o arquivo de ambiente:

```bash
cp .env.example .env
```

Suba os containers do projeto com o Sail:

```bash
./vendor/bin/sail up -d
```

Instale as dependências do PHP:

```bash
./vendor/bin/sail composer install
```

Gere a key da aplicação:

```bash
./vendor/bin/sail artisan key:generate
```

Rode as migrations:

```bash
./vendor/bin/sail artisan migrate
```

Rode o seeder na database:

```bash
./vendor/bin/sail artisan db:seed
```

### Execução em Ambiente de Desenvolvimento

```bash
./vendor/bin/sail up
```

Acesse a aplicação em: [http://localhost](http://localhost)

### Testes

```bash
./vendor/bin/sail artisan test
```

## 🛠️ Estrutura Padrão do Projeto

```
.
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── tests/
├── docker-compose.yml
├── sail
├── .env
├── composer.json
└── README.md
```

## ✅ Funcionalidades

* 🔐 API com autenticação via Sanctum
* 📄 CRUD completo para os módulos do sistema
* 📨 Integração com sistema de emails (via Mailpit)
* ⚙️ Migrations e seeders
* 🐳 Ambiente dockerizado com Sail

## 📚 Comandos Úteis

| Comando                             | Descrição                                      |
| ----------------------------------- | ---------------------------------------------- |
| `./vendor/bin/sail up -d`           | Sobe os containers em background               |
| `./vendor/bin/sail artisan migrate` | Roda as migrations                             |
| `./vendor/bin/sail artisan test`    | Executa os testes                              |
| `./vendor/bin/sail npm run dev`     | Compila os assets (se usar frontend integrado) |

## 📦 Deploy

O projeto pode ser facilmente deployado em servidores que suportam Docker ou diretamente em plataformas como DigitalOcean, utilizando o mesmo ambiente configurado no Sail.

---

## ✍️ Autores

* **Pedro Sudario** - [@petersudario](https://github.com/petersudario)
* **Enzo Enrico** - [@enzoenrico](https://github.com/enzoenrico)
* **Guilherme Sampaio** - [@guiguitatu](https://github.com/guiguitatu)
* **Laura Santos** - [@kyoulau](https://github.com/kyoulau)

---

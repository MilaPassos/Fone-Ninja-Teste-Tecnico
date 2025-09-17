# 🧪 Desafio Técnico – Laravel + Vue

Bem-vindo(a) ao desafio! Este repositório contém um sistema ERP de estoque com backend em **Laravel** e frontend em **Vue 3**.

## ⚡ Como rodar o projeto

### 1. Pré-requisitos
- PHP >= 8.2
- Composer
- Node.js >= 18
- npm ou yarn
- MySQL

### 2. Clonando o repositório
```bash
git clone https://github.com/MilaPassos/Fone-Ninja-Teste-Tecnico
```

---

## 🚀 Backend (Laravel)

### Instalação
```bash
cd backend
composer install
```

### Configuração do ambiente

####  MySQL 
1. Crie um banco no MySQL (ex: `erp_estoque`).
2. Copie o arquivo `.env.example` para `.env` (ou crie um novo `.env`).
3. Edite as variáveis de banco no `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=erp_estoque
   DB_USERNAME=seu_usuario
   DB_PASSWORD=sua_senha
   ```
4. Rode as migrations normalmente:
   ```bash
   php artisan migrate
   ```

### Gerando chave da aplicação
```bash
php artisan key:generate
```

### (Opcional) Popular banco com dados fake
```bash
php artisan db:seed
```

### Rodando o servidor
```bash
php artisan serve
```

A API estará disponível em `http://localhost:8000`.

---

## 💻 Frontend (Vue 3 + Vite)

### Instalação
```bash
cd ../frontend
npm install
```

### Edite as variaveis env 
Copie o arquivo `.env.example` para `.env` (ou crie um novo `.env`).

### Rodando o frontend
```bash
npm run dev
```

Acesse `http://localhost:5173` no navegador.

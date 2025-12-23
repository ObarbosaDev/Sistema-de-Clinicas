# PROJETO PESSOAL – Sistema de Gestão de Clínica Médica

## Visão Geral

Este projeto consiste em um **sistema web de gestão para clínica médica**, desenvolvido em **PHP puro**, com **MySQL** como banco de dados e **Bootstrap** para estilização.
O sistema contempla funcionalidades essenciais como:

* Cadastro de pacientes
* Cadastro de médicos
* Agendamento de consultas
* Agenda diária
* Controle de disponibilidade médica
* Autenticação de usuários

O objetivo do projeto é consolidar conhecimentos em **desenvolvimento web backend**, **modelagem de banco de dados** e **integração PHP + MySQL**, simulando um sistema real de clínica.

---

## Tecnologias Utilizadas

* **PHP 8.x**
* **MySQL / MariaDB**
* **HTML5**
* **CSS3**
* **Bootstrap 5**
* **JavaScript (básico)**
* **Apache (XAMPP / WAMP / Laragon)**

---

## Estrutura do Projeto

```
PROJETO PESSOAL/
│
├── agenda-diaria.php
├── banco-de-dados.sql
├── cadastrar-consulta.php
├── cadastrar-medico.php
├── cadastrar-paciente.php
├── calendario-disponibilidade.php
├── config.php
├── login.php
├── logout.php
├── css/
│   └── arquivos do Bootstrap
└── demais arquivos auxiliares
```

---

## Requisitos para Execução

Antes de iniciar, certifique-se de possuir:

* PHP 8.x instalado
* Servidor Apache ativo
* MySQL ou MariaDB
* Gerenciador de banco (phpMyAdmin, MySQL Workbench, etc.)

Recomendado:

* **XAMPP** ou **Laragon** (Windows)
* **Docker** (opcional, para evolução futura)

---

## Configuração do Ambiente

### 1. Clonar ou copiar o projeto

Coloque a pasta do projeto dentro do diretório do servidor web:

**XAMPP**

```
C:\xampp\htdocs\PROJETO_PESSOAL
```

**Laragon**

```
C:\laragon\www\PROJETO_PESSOAL
```

---

### 2. Criar o banco de dados

1. Abra o **phpMyAdmin**
2. Crie um banco de dados, por exemplo:

```sql
CREATE DATABASE clinica;
```

3. Importe o arquivo:

```
banco-de-dados.sql
```

Esse arquivo já contém as tabelas necessárias para o funcionamento do sistema.

---

### 3. Configurar a conexão com o banco

Abra o arquivo:

```
config.php
```

Ajuste as credenciais conforme seu ambiente:

```php
define("DB_HOST", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "clinica");
```

---

### 4. Iniciar o servidor

Inicie o Apache e o MySQL pelo painel do XAMPP/Laragon.

Acesse no navegador:

```
http://localhost/PROJETO_PESSOAL
```

---

## Funcionalidades do Sistema

### Cadastro

* Pacientes
* Médicos

### Consultas

* Agendamento de consultas
* Verificação de disponibilidade
* Agenda diária

### Autenticação

* Sistema de login
* Controle de sessão
* Logout seguro

---

## Segurança Básica Implementada

* Uso de `session_start()` para controle de acesso
* Redirecionamento para login quando não autenticado
* Separação da configuração de banco em arquivo dedicado

---

## Possíveis Evoluções Futuras

* Refatoração para **Laravel**
* Implementação de **MVC**
* Criptografia de senhas com `password_hash`
* Sistema de permissões (admin, médico, recepção)
* Dashboard com gráficos
* API REST
* Deploy em servidor cloud (Render, Railway, etc.)


## Autor

**Matheus Barbosa**
Estudante de Análise e Desenvolvimento de Sistemas
Desenvolvimento Web | PHP | MySQL | Backend

Projeto desenvolvido com finalidade **acadêmica e prática**, simulando um sistema real de clínica médica.

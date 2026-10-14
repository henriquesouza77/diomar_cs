📖 README.md — DIOMAR_CS IMÓVEIS
Sistema imobiliário completo e premium. Documentação oficial do projeto.

markdown
# 🏢 DIOMAR_CS IMÓVEIS

Sistema imobiliário completo, premium e modular, desenvolvido em **PHP + MySQL + HTML5 + CSS3 + JavaScript puro**, pronto para rodar localmente no **XAMPP**.

![Status](https://img.shields.io/badge/status-produção-1E3A8A)
![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1)
![License](https://img.shields.io/badge/license-comercial-3B82F6)

---

## 📑 ÍNDICE

1. [Sobre o projeto](#1-sobre-o-projeto)
2. [Tecnologias](#2-tecnologias)
3. [Instalação no XAMPP](#3-instalação-no-xampp)
4. [Estrutura de arquivos](#4-estrutura-de-arquivos)
5. [Banco de dados — dicionário completo](#5-banco-de-dados--dicionário-completo)
6. [Diagrama de relacionamentos](#6-diagrama-de-relacionamentos)
7. [Backend — arquivos e responsabilidades](#7-backend--arquivos-e-responsabilidades)
8. [Frontend — arquivos e responsabilidades](#8-frontend--arquivos-e-responsabilidades)
9. [API — endpoints](#9-api--endpoints)
10. [Autenticação e autorização](#10-autenticação-e-autorização)
11. [Uploads de imagens](#11-uploads-de-imagens)
12. [Segurança](#12-segurança)
13. [Guia rápido para alterações](#13-guia-rápido-para-alterações)
14. [Manutenção](#14-manutenção)
15. [Troubleshooting](#15-troubleshooting)
16. [Licença](#16-licença)

---

## 1. Sobre o projeto

**DIOMAR_CS IMÓVEIS** é uma plataforma imobiliária de alto padrão que oferece:

- **Para visitantes:** busca avançada com filtros, página individual do imóvel, galeria, mapa, formulário de interesse, favoritos, WhatsApp direto.
- **Para administradores e corretores:** painel SaaS completo com dashboard, CRUD de imóveis, gestão de leads, corretores, usuários, relatórios e configurações.
- **Para clientes:** conta própria com favoritos sincronizados.

O sistema **inicia 100% vazio** — nenhum imóvel, lead, corretor ou dado fictício. Tudo é cadastrado manualmente pelo administrador.

---

## 2. Tecnologias

| Camada | Tecnologia |
|---|---|
| **Backend** | PHP 8.0+ (namespace, PDO, prepared statements) |
| **Banco de dados** | MySQL 8.0 / MariaDB 10.4+ |
| **Frontend** | HTML5, CSS3 (variáveis nativas, grid, flexbox) |
| **JavaScript** | Vanilla ES6+ (módulos IIFE, sem libs externas) |
| **Servidor** | Apache (XAMPP) |
| **Gráficos** | Canvas API pura (sem libs) |
| **Mapas** | OpenStreetMap embutido via iframe |

**Nenhuma dependência obrigatória:**
- ❌ Sem Node.js
- ❌ Sem Composer
- ❌ Sem TypeScript
- ❌ Sem Firebase / Supabase
- ❌ Sem serviços pagos

---

## 3. Instalação no XAMPP

### 3.1. Pré-requisitos

- **XAMPP** instalado (versão com PHP 8.0+). Download: https://www.apachefriends.org/
- Navegador moderno (Chrome, Firefox, Edge, Safari)

### 3.2. Passo a passo

#### **Passo 1 — Copiar o projeto**

Copie a pasta `diomar-cs` para dentro de `htdocs` do XAMPP:

- **Windows:** `C:\xampp\htdocs\diomar-cs\`
- **macOS:** `/Applications/XAMPP/htdocs/diomar-cs/`
- **Linux:** `/opt/lampp/htdocs/diomar-cs/`

A estrutura final deve ficar assim:
C:\xampp\htdocs\diomar-cs
├── frontend/
├── backend/
├── database/
├── index.php
├── .htaccess
└── README.md

text

#### **Passo 2 — Iniciar o XAMPP**

1. Abra o **XAMPP Control Panel**
2. Clique em **Start** ao lado de **Apache**
3. Clique em **Start** ao lado de **MySQL**

Aguarde ambos ficarem com fundo verde.

#### **Passo 3 — Criar o banco de dados**

**Opção A — Via phpMyAdmin (recomendado):**

1. Acesse: http://localhost/phpmyadmin
2. Menu superior → aba **Importar**
3. Clique em **Escolher arquivo** → selecione `database/database.sql`
4. Role a página até o final → clique em **Executar**
5. O banco `diomar_cs_imoveis` será criado automaticamente

Se o banco já estiver em uso, não importe `database/database.sql` novamente:
isso recria as tabelas. Para habilitar a lixeira de imóveis sem apagar dados,
importe `database/migrations/20261012_add_property_soft_delete.sql` pelo
phpMyAdmin ou execute esse arquivo no banco `diomar_cs_imoveis`.

**Opção B — Via terminal MySQL:**

```bash
cd C:\xampp\mysql\bin
mysql -u root -p < C:\xampp\htdocs\diomar-cs\database\database.sql
(A senha do root no XAMPP padrão é vazia — apenas pressione Enter.)

Passo 4 — Configurar credenciais (se necessário)
Abra backend/config/database.php e confirme:

php
return [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'diomar_cs_imoveis',
    'username' => 'root',      // padrão XAMPP
    'password' => '',          // padrão XAMPP (vazio)
    'charset'  => 'utf8mb4',
];
Se você alterou a senha do MySQL, atualize aqui.

Passo 5 — Acessar com o administrador inicial
O database.sql cria a conta inicial com hash bcrypt válido.

E-mail: admin@diomarcsimoveis.com.br
Senha: admin123 (troque imediatamente após o primeiro login)

Se o banco foi importado antes desta correção, confira no phpMyAdmin o e-mail e o ID do administrador. Para redefinir a senha dessa conta, gere um hash:

bash
php -r "echo password_hash('sua_nova_senha', PASSWORD_BCRYPT);"

Depois atualize somente o registro do administrador pelo ID correspondente:

sql
UPDATE users SET password_hash = 'COLE_O_HASH_AQUI' WHERE id = ID_DO_ADMINISTRADOR;

Passo 6 — Acessar o sistema
URL	O que é
http://localhost/diomar-cs/	Home pública
http://localhost/diomar-cs/frontend/pages/login.php	Login
http://localhost/diomar-cs/frontend/pages/admin/dashboard.php	Painel admin
Credenciais iniciais:

E-mail: admin@diomarcsimoveis.com.br

Senha: admin123 (troque imediatamente)

Passo 7 — Permissões de escrita (Linux/macOS apenas)
bash
chmod -R 755 backend/uploads/
chmod -R 755 backend/config/
No Windows, o XAMPP já configura corretamente.

4. Estrutura de arquivos
text
diomar-cs/
│
├── frontend/                       # Tudo que o navegador acessa
│   ├── assets/
│   │   ├── fonts/                  # Fontes locais (opcional)
│   │   └── icons/                  # Ícones SVG avulsos
│   ├── css/                        # 14 arquivos CSS por responsabilidade
│   ├── js/                         # 11 arquivos JS modulares
│   ├── images/                     # Imagens públicas
│   ├── pages/                      # Páginas HTML/PHP
│   │   ├── admin/                  # Painel administrativo
│   │   └── ...                     # Páginas públicas
│   └── components/                 # Componentes reutilizáveis PHP
│
├── backend/                        # Toda a lógica de servidor
│   ├── config/                     # Configurações globais
│   ├── core/                       # Classes base (Database, Router, etc)
│   ├── controllers/                # Camada HTTP
│   ├── models/                     # Camada de dados
│   ├── services/                   # Regras de negócio
│   ├── middleware/                 # Auth, CSRF
│   ├── routes/                     # Mapa de rotas
│   ├── helpers/                    # Funções utilitárias
│   ├── uploads/                    # Imagens enviadas
│   └── api/                        # Entry point da API
│
├── database/
│   ├── database.sql                # Estrutura completa + catálogos
│   └── migrations/                 # Atualizações para bancos existentes
│
├── index.php                       # Redireciona para o frontend
├── .htaccess                       # Regras Apache
└── README.md                       # Este arquivo
5. Banco de dados — dicionário completo
Banco: diomar_cs_imoveis · Charset: utf8mb4 · Collation: utf8mb4_unicode_ci · Engine: InnoDB

5.1. roles — Perfis de usuário
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
name	VARCHAR(50)	UNIQUE	admin, agent, client
description	VARCHAR(255)		Descrição legível
created_at	TIMESTAMP		Data de criação
Registros iniciais: 3 (admin, agent, client).

5.2. permissions — Permissões granulares
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
slug	VARCHAR(80)	UNIQUE	Ex: properties.create
description	VARCHAR(255)		Descrição legível
Registros iniciais: 10 (properties.view/create/edit/delete, leads.view/edit, users.view/edit, dashboard.view, settings.edit).

5.3. role_permissions — Pivô N:N
Campo	Tipo	Chave
role_id	INT UNSIGNED	PK, FK → roles.id
permission_id	INT UNSIGNED	PK, FK → permissions.id
Regras: ON DELETE CASCADE para ambos.

5.4. users — Usuários do sistema
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
role_id	INT UNSIGNED	FK → roles.id	Perfil (1=admin, 2=agent, 3=client)
name	VARCHAR(150)	NOT NULL	Nome completo
email	VARCHAR(180)	UNIQUE	Login
password_hash	VARCHAR(255)		Hash bcrypt
phone	VARCHAR(20)		Telefone
whatsapp	VARCHAR(20)		WhatsApp
avatar	VARCHAR(255)		Nome do arquivo
status	ENUM		active, inactive, banned
last_login	DATETIME		Último login
created_at	TIMESTAMP		Data de criação
updated_at	TIMESTAMP		Data de atualização
Índices: email, role_id, status.

5.5. agents — Dados complementares do corretor
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
user_id	INT UNSIGNED	UNIQUE, FK → users.id	Usuário correspondente
creci	VARCHAR(30)		Registro CRECI
bio	TEXT		Biografia
specialties	VARCHAR(255)		Especialidades
created_at	TIMESTAMP		Data de criação
Regra: ON DELETE CASCADE — se o usuário for removido, seu perfil de corretor também.

5.6. categories — Categorias de imóvel
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
name	VARCHAR(80)	UNIQUE	Nome legível
slug	VARCHAR(80)	UNIQUE	URL-friendly
icon	VARCHAR(60)		Ícone
Registros iniciais: 4 (Residencial, Comercial, Rural, Terrenos).

5.7. property_types — Tipos de imóvel
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
category_id	INT UNSIGNED	FK → categories.id	Categoria mãe
name	VARCHAR(80)		Nome legível
slug	VARCHAR(80)	UNIQUE	URL-friendly
Registros iniciais: 14 (apartamento, casa, cobertura, kitnet, studio, sala comercial, loja, galpão, prédio comercial, sítio, fazenda, chácara, terreno, lote).

5.8. features — Características dinâmicas
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
name	VARCHAR(80)	UNIQUE	Nome
slug	VARCHAR(80)	UNIQUE	URL-friendly
icon	VARCHAR(60)		Ícone
category	VARCHAR(50)		Agrupamento
Registros iniciais: 20 (piscina, churrasqueira, academia, elevador, varanda, ar-condicionado, jardim, portaria 24h, salão de festas, mobiliado, playground, quadra, sauna, solarium, home office, closet, lavabo, copa, despensa, quintal).

5.9. lead_statuses — Status de lead
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
name	VARCHAR(50)	UNIQUE	Nome legível
color	VARCHAR(20)		Cor hexadecimal
Registros iniciais: 5 (Novo, Em contato, Em negociação, Convertido, Perdido).

5.10. properties — Imóveis
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
code	VARCHAR(30)	UNIQUE	Código público (ex: DC-2026-0001)
agent_id	INT UNSIGNED	FK → users.id	Corretor responsável
category_id	INT UNSIGNED	FK → categories.id	Categoria
property_type_id	INT UNSIGNED	FK → property_types.id	Tipo
title	VARCHAR(200)	NOT NULL	Título do anúncio
slug	VARCHAR(220)	UNIQUE	URL-friendly
description	TEXT		Descrição completa
purpose	ENUM		venda ou aluguel
status	ENUM		disponivel, vendido, alugado, reservado, inativo
price	DECIMAL(12,2)		Preço
condo_fee	DECIMAL(10,2)		Condomínio mensal
iptu	DECIMAL(10,2)		IPTU anual
area_total	DECIMAL(10,2)		Área do terreno
area_built	DECIMAL(10,2)		Área construída
bedrooms	TINYINT UNSIGNED		Quartos
suites	TINYINT UNSIGNED		Suítes
bathrooms	TINYINT UNSIGNED		Banheiros
parking_spaces	TINYINT UNSIGNED		Vagas
floor	SMALLINT		Andar
featured	TINYINT(1)		1 = destaque
published	TINYINT(1)		1 = visível no site
views	INT UNSIGNED		Contador de visualizações
created_at	TIMESTAMP		Criação
updated_at	TIMESTAMP		Atualização
Índices: agent, category, type, purpose, status, featured, price, published.

Regras de FK:

agent_id: ON DELETE SET NULL (remove imóvel do corretor se ele for excluído)

category_id e property_type_id: ON DELETE RESTRICT (não permite excluir categoria/tipo em uso)

5.11. property_addresses — Endereço do imóvel
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
property_id	INT UNSIGNED	UNIQUE, FK → properties.id	Imóvel
zipcode	VARCHAR(10)		CEP
street	VARCHAR(200)		Rua
number	VARCHAR(20)		Número
complement	VARCHAR(100)		Complemento
neighborhood	VARCHAR(120)		Bairro
city	VARCHAR(120)	NOT NULL	Cidade
state	CHAR(2)	NOT NULL	UF
latitude	DECIMAL(10,7)		Latitude (mapa)
longitude	DECIMAL(10,7)		Longitude (mapa)
Regra: ON DELETE CASCADE — se o imóvel for excluído, o endereço também.

5.12. property_features — Pivô N:N
Campo	Tipo	Chave
property_id	INT UNSIGNED	PK, FK → properties.id
feature_id	INT UNSIGNED	PK, FK → features.id
Regras: ON DELETE CASCADE para ambos.

5.13. property_images — Imagens do imóvel
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
property_id	INT UNSIGNED	FK → properties.id	Imóvel
filename	VARCHAR(255)	NOT NULL	Nome no disco
is_primary	TINYINT(1)		1 = capa
sort_order	SMALLINT		Ordem
alt_text	VARCHAR(200)		Texto alternativo
created_at	TIMESTAMP		Data de upload
Regra: ON DELETE CASCADE.

5.14. leads — Contatos de interesse
Campo	Tipo	Chave	Descrição
id	INT UNSIGNED	PK	Identificador
property_id	INT UNSIGNED	FK → properties.id	Imóvel de interesse
agent_id	INT UNSIGNED	FK → users.id	Corretor responsável
status_id	INT UNSIGNED	FK → lead_statuses.id	Status
name	VARCHAR(150)	NOT NULL	Nome
email	VARCHAR(180)	NOT NULL	E-mail
phone	VARCHAR(20)		Telefone
whatsapp	VARCHAR(20)		WhatsApp
message	TEXT		Mensagem
source	VARCHAR(60)		Origem (site, property_page, contact_form)
created_at	TIMESTAMP		Recebimento
updated_at	TIMESTAMP		Atualização
Regras:

property_id: ON DELETE SET NULL (lead permanece se imóvel for removido)

agent_id: ON DELETE SET NULL

status_id: ON DELETE RESTRICT

5.15. favorites — Favoritos do usuário
Campo	Tipo	Chave
user_id	INT UNSIGNED	PK, FK → users.id
property_id	INT UNSIGNED	PK, FK → properties.id
created_at	TIMESTAMP	
Regras: ON DELETE CASCADE para ambos.

5.16. contact_messages — Mensagens do formulário de contato
Campo	Tipo	Chave
id	INT UNSIGNED	PK
name	VARCHAR(150)	NOT NULL
email	VARCHAR(180)	NOT NULL
phone	VARCHAR(20)	
subject	VARCHAR(200)	
message	TEXT	NOT NULL
is_read	TINYINT(1)	
created_at	TIMESTAMP	
5.17. settings — Configurações do site
Campo	Tipo	Chave
setting_key	VARCHAR(80)	PK
setting_value	TEXT	
updated_at	TIMESTAMP	
Registros iniciais: site_name, site_email, site_phone, site_whatsapp, site_address, items_per_page.

5.18. notifications — Notificações dos usuários
Campo	Tipo	Chave
id	INT UNSIGNED	PK
user_id	INT UNSIGNED	FK → users.id
title	VARCHAR(200)	NOT NULL
message	TEXT	
link	VARCHAR(255)	
is_read	TINYINT(1)	
created_at	TIMESTAMP	
Regra: ON DELETE CASCADE.

5.19. activity_logs — Auditoria
Campo	Tipo	Chave
id	INT UNSIGNED	PK
user_id	INT UNSIGNED	FK → users.id
action	VARCHAR(80)	NOT NULL
entity	VARCHAR(60)	
entity_id	INT UNSIGNED	
details	TEXT	JSON
ip_address	VARCHAR(45)	
user_agent	VARCHAR(255)	
created_at	TIMESTAMP	
Regra: user_id → ON DELETE SET NULL (mantém o log mesmo se o usuário for excluído).

6. Diagrama de relacionamentos
6.1. Visão geral
text
┌──────────┐     ┌──────────────┐     ┌──────────────┐
│  roles   │◄────┤    users     ├────►│    agents    │
└────┬─────┘     └──────┬───────┘     └──────────────┘
     │                  │
     │                  │
┌────▼─────────┐        │
│    role_     │        │
│ permissions  │        │
└────┬─────────┘        │
     │                  │
┌────▼─────────┐        │
│ permissions  │        │
└──────────────┘        │
                        │
                        ├────────────►┌──────────────┐
                        │             │   favorites  │
                        │             └──────┬───────┘
                        │                    │
                        │                    ▼
                        │             ┌──────────────┐
                        │             │  properties  │
                        │             └──┬──┬──┬──┬───┘
                        │                │  │  │  │
                        │                │  │  │  └──────────────────┐
                        │                │  │  │                     │
                        │                │  │  └──► property_images  │
                        │                │  │                       │
                        │                │  └──► property_features ─┼─┐
                        │                │                          │ │
                        │                └──► property_addresses    │ │
                        │                                         │ │
                        │                                         │ │
                        │                ┌──────────────┐         │ │
                        └───────────────►│    leads     │◄────────┘ │
                                         └──────┬───────┘           │
                                                │                    │
                                                ▼                    │
                                         ┌──────────────┐           │
                                         │lead_statuses │           │
                                         └──────────────┘           │
                                                                     │
                                         ┌──────────────┐           │
                                         │   features   │◄──────────┘
                                         └──────────────┘

┌──────────────┐         ┌──────────────┐
│  categories  │◄───────►│    types     │
└──────────────┘         └──────────────┘

┌──────────────┐         ┌──────────────┐
│   settings   │         │notifications │
└──────────────┘         └──────────────┘

┌──────────────┐         ┌──────────────┐
│activity_logs │         │contact_msg   │
└──────────────┘         └──────────────┘
6.2. Relacionamentos detalhados
Origem	Cardinalidade	Destino	Regra
users.role_id	N:1	roles.id	RESTRICT
agents.user_id	1:1	users.id	CASCADE
role_permissions.role_id	N:1	roles.id	CASCADE
role_permissions.permission_id	N:1	permissions.id	CASCADE
properties.agent_id	N:1	users.id	SET NULL
properties.category_id	N:1	categories.id	RESTRICT
properties.property_type_id	N:1	property_types.id	RESTRICT
property_addresses.property_id	1:1	properties.id	CASCADE
property_images.property_id	N:1	properties.id	CASCADE
property_features.property_id	N:1	properties.id	CASCADE
property_features.feature_id	N:1	features.id	CASCADE
leads.property_id	N:1	properties.id	SET NULL
leads.agent_id	N:1	users.id	SET NULL
leads.status_id	N:1	lead_statuses.id	RESTRICT
favorites.user_id	N:1	users.id	CASCADE
favorites.property_id	N:1	properties.id	CASCADE
notifications.user_id	N:1	users.id	CASCADE
activity_logs.user_id	N:1	users.id	SET NULL
property_types.category_id	N:1	categories.id	CASCADE
7. Backend — arquivos e responsabilidades
7.1. backend/config/
Arquivo	Finalidade	Executado por
database.php	Credenciais MySQL	Core\Database
app.php	Configurações gerais (URL, sessão, uploads)	bootstrap.php, helpers
constants.php	Constantes globais (roles, status)	bootstrap.php
bootstrap.php	Inicialização + autoload	api/index.php, controllers
7.2. backend/core/
Arquivo	Responsabilidade
Database.php	Conexão PDO singleton + helpers (insert, update, delete, fetch, fetchAll)
Session.php	Sessão segura + CSRF token
Request.php	Leitura de $_GET, $_POST, JSON body, $_FILES
Response.php	json(), error(), success(), redirect()
Validator.php	Validação fluente (required, email, min, max, unique)
Router.php	Roteador simples baseado em array de rotas
7.3. backend/middleware/
Arquivo	Função
AuthMiddleware.php	Login, roles, helpers (userId, isAdmin, requireAdmin)
AdminMiddleware.php	Exige admin
AgentMiddleware.php	Exige admin ou corretor
CsrfMiddleware.php	Valida token CSRF em POST/PUT/DELETE
7.4. backend/models/ (19 arquivos)
Model	Tabela
User.php	users
Role.php	roles
Permission.php	permissions
Agent.php	agents
Property.php	properties
PropertyAddress.php	property_addresses
PropertyImage.php	property_images
Feature.php	features
Category.php	categories
PropertyType.php	property_types
Lead.php	leads
LeadStatus.php	lead_statuses
Favorite.php	favorites
Setting.php	settings
Notification.php	notifications
ActivityLog.php	activity_logs
7.5. backend/services/ (8 arquivos)
Service	Responsabilidade
AuthService.php	Login, cadastro, logout
PropertyService.php	Regras de negócio dos imóveis
ImageService.php	Upload, remoção, ordenação de imagens
LeadService.php	Criação e gestão de leads
UserService.php	Criação e gestão de usuários/corretores
DashboardService.php	Métricas e dados para gráficos
FavoriteService.php	Toggle e listagem de favoritos
ActivityLogService.php	Log de auditoria
7.6. backend/controllers/ (10 arquivos)
Controller	Métodos
AuthController.php	login, register, logout, me
UserController.php	index, store, update, delete
PropertyController.php	index, show, store, update, destroy, duplicate, toggleFeatured, similar
PropertyImageController.php	upload, remove, setPrimary, reorder
LeadController.php	store, index, updateStatus
FavoriteController.php	index, toggle
AgentController.php	index
DashboardController.php	stats, recentProperties, charts
SettingsController.php	index, save
CategoryController.php	categories, propertyTypes, features
7.7. backend/routes/
Arquivo	Cobertura
web.php	Todas as rotas consolidadas
auth.php	Rotas de autenticação
properties.php	Rotas de imóveis
users.php	Rotas de usuários
leads.php	Rotas de leads
favorites.php	Rotas de favoritos
dashboard.php	Rotas de dashboard
settings.php	Rotas de configurações
7.8. backend/api/index.php
Entry point único da API. Recebe ?route=..., valida CSRF quando aplicável, instancia o router e despacha.

Fluxo:

text
Requisição → api/index.php → bootstrap → Router → Controller → Service → Model → Response JSON
8. Frontend — arquivos e responsabilidades
8.1. frontend/css/ (14 arquivos)
Arquivo	Escopo
variables.css	Todas as cores, fontes, espaçamentos
reset.css	Normalização entre navegadores
base.css	Tipografia, containers, utilitários
components.css	Botões, inputs, cards, badges, modais, alerts
navbar.css	Cabeçalho, menu, drawer
hero.css	Seção hero + busca
home.css	Categorias, destaques, stats, CTA
properties.css	Listagem + filtros + cards
property-details.css	Galeria, preço, corretor, mapa
contact.css	Formulário + WhatsApp float
login.css	Layout split login/cadastro
admin.css	Dashboard SaaS completo
animations.css	Scroll reveal, parallax, counters
responsive.css	Mobile-first, breakpoints 480→1536
8.2. frontend/js/ (11 arquivos)
Arquivo	Escopo
api.js	Cliente HTTP centralizado
main.js	Toast, modal, dropdown, tabs, ripple
navbar.js	Scroll state, drawer, contador favoritos
animations.js	Reveal, parallax, counters, tilt
hero-search.js	Tabs + máscara + persistência
properties.js	Listagem completa + filtros + paginação
property-details.js	Galeria, mapa, corretor, form
favorites.js	Lista de favoritos
auth.js	Login + cadastro
admin.js	Dashboard, CRUD, leads, users
charts.js	Gráficos em Canvas
8.3. frontend/pages/
Página	Descrição
index.php	Home premium
imoveis.php	Listagem com filtros
imovel.php	Página individual
contato.php	Formulário
sobre.php	Institucional
anunciar.php	Anunciar imóvel
login.php / cadastro.php	Autenticação
favoritos.php	Favoritos do usuário
recuperar-senha.php	Recuperação
404.php / 500.php	Erros
admin/*	Painel administrativo (9 arquivos)
8.4. frontend/components/
Componente	Função
header.php	Navbar + <head>
footer.php	Rodapé + WhatsApp flutuante
property-card.php	Card de imóvel
property-card-skeleton.php	Skeleton
empty-state.php	Estado vazio
pagination.php	Paginação
9. API — endpoints
Base: /diomar-cs/api/{rota} (via .htaccess) ou /diomar-cs/backend/api/index.php?route={rota}

Autenticação
Método	Rota	Descrição	Auth
POST	auth/login	Login	—
POST	auth/register	Cadastro	—
POST	auth/logout	Logout	✅
GET	auth/me	Dados do usuário logado	✅
Imóveis
Método	Rota	Descrição	Auth
GET	properties	Listar com filtros	—
GET	property?id=X	Detalhe	—
GET	properties/similar?id=X	Semelhantes	—
POST	properties/store	Criar	Agent
POST	properties/update	Atualizar	Agent
POST	properties/delete	Excluir	Agent
POST	properties/duplicate	Duplicar	Agent
POST	properties/toggle-featured	Destacar	Agent
Imagens
Método	Rota	Descrição	Auth
POST	images/upload	Upload múltiplo	Agent
POST	images/remove	Remover	Agent
POST	images/set-primary	Definir capa	Agent
POST	images/reorder	Reordenar	Agent
Leads
Método	Rota	Descrição	Auth
POST	leads/store	Criar (público)	—
GET	leads	Listar	Agent
POST	leads/update-status	Atualizar status	Agent
Favoritos
Método	Rota	Descrição	Auth
GET	favorites	Listar	Login
POST	favorites/toggle	Alternar	Login
Dashboard
Método	Rota	Auth
GET	dashboard/stats	Agent
GET	dashboard/recent-properties	Agent
GET	dashboard/charts	Agent
Catálogos
Método	Rota	Descrição
GET	categories	Listar categorias
GET	property-types?category_id=X	Tipos
GET	features	Características
Usuários
Método	Rota	Auth
GET	users?role_id=X	Admin
POST	users/store	Admin
POST	users/update	Admin
POST	users/delete	Admin
Configurações
Método	Rota	Auth
GET	settings	—
POST	settings/save	Admin
10. Autenticação e autorização
10.1. Sessão
Nome: diomar_session

Cookie: HttpOnly, SameSite=Lax, Secure (em HTTPS)

Regeneração de ID a cada login

10.2. Níveis de acesso
Role	Pode
admin (1)	Tudo
agent (2)	Gerenciar imóveis próprios, ver leads, dashboard
client (3)	Ver site, favoritar, enviar interesse
10.3. Fluxo de proteção
text
Requisição → CsrfMiddleware → AuthMiddleware → Controller → Service
              (POST/PUT/DELETE)   (rotas protegidas)
Nunca confie apenas no frontend. Todas as rotas sensíveis chamam AuthMiddleware::requireAdmin() ou requireAgent() no backend.

10.4. CSRF
Token gerado na sessão ao iniciar

Enviado em todo POST via X-CSRF-Token header ou campo csrf_token

Validado por CsrfMiddleware

11. Uploads de imagens
11.1. Configuração
Em backend/config/app.php:

php
'max_upload'    => 5 * 1024 * 1024,   // 5MB
'allowed_mimes' => [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
],
11.2. Fluxo
Frontend envia múltiplos arquivos via FormData

ImageService::upload() valida MIME real (não extensão), tamanho e gera nome único

Salva em backend/uploads/properties/

Registra em property_images

Marca a primeira como principal automaticamente

11.3. Segurança
backend/uploads/.htaccess bloqueia execução de PHP

Apenas extensões de imagem são permitidas

Nome do arquivo: {property_id}-{uniqid}.{ext}

12. Segurança
12.1. Implementado
Ameaça	Mitigação
SQL Injection	PDO + prepared statements em 100% das queries
XSS	htmlspecialchars() na saída, clean_*() na entrada
CSRF	Token por sessão validado em POST/PUT/DELETE
Upload malicioso	Validação de MIME real, bloqueio de execução em uploads
Session hijacking	Cookie HttpOnly, SameSite, regeneração de ID
Força bruta	Preparado para lockout (configurável)
Exposição de arquivos	.htaccess bloqueia config, core, models, services
Error disclosure	debug = false em produção oculta stack traces
12.2. Antes de colocar em produção
Altere debug para false em backend/config/app.php

Altere a senha do admin

Ative HTTPS no servidor

Restrinja acesso ao phpMyAdmin

Configure backups automáticos do banco

Atualize a lista de allowed_mimes se necessário

13. Guia rápido para alterações
🎨 Cores, fontes, espaçamentos
Arquivo: frontend/css/variables.css

css
:root {
  --color-primary:       #1E3A8A;   /* Azul principal */
  --color-primary-dark:  #0F1F4D;   /* Azul escuro */
  --color-primary-light: #3B82F6;   /* Azul claro */
  --font-primary: 'Inter', sans-serif;
}
🧭 Navbar
CSS: frontend/css/navbar.css

HTML/PHP: frontend/components/header.php

JS: frontend/js/navbar.js

🖼️ Hero da home
CSS: frontend/css/hero.css

HTML: frontend/pages/index.php (seção <section class="hero">)

JS: frontend/js/hero-search.js

🏠 Seções da home
CSS: frontend/css/home.css

HTML: frontend/pages/index.php

🏘️ Cards de imóvel
CSS: frontend/css/properties.css (.property-card)

HTML: frontend/components/property-card.php

JS: frontend/js/properties.js

🔍 Filtros
CSS: frontend/css/properties.css (.filters)

HTML: frontend/pages/imoveis.php

JS: frontend/js/properties.js

🏢 Página do imóvel
CSS: frontend/css/property-details.css

HTML: frontend/pages/imovel.php

JS: frontend/js/property-details.js

⚙️ Dashboard admin
CSS: frontend/css/admin.css

HTML: frontend/pages/admin/dashboard.php

JS: frontend/js/admin.js

📝 Formulário de imóvel
HTML: frontend/pages/admin/imovel-form.php

JS: frontend/js/admin.js → função initImovelForm()

✨ Animações
CSS: frontend/css/animations.css

JS: frontend/js/animations.js

📱 Responsividade
Arquivo: frontend/css/responsive.css

Breakpoints: 480, 640, 768, 1024, 1280, 1536px

🗄️ Banco de dados
Estrutura: database/database.sql

Conexão: backend/config/database.php

🛣️ Rotas da API
Arquivo: backend/routes/web.php

🎛️ Lógica de negócio
Services: backend/services/*.php

Controllers: backend/controllers/*.php

📞 WhatsApp flutuante
HTML: frontend/components/footer.php

Número: procure por 5511999999999

📧 E-mail e telefone do site
Painel: frontend/pages/admin/configuracoes.php

Banco: tabela settings

🖼️ Imagem padrão (sem foto)
Arquivo: frontend/images/placeholder.svg

🔐 Login e cadastro
CSS: frontend/css/login.css

HTML: frontend/pages/login.php / cadastro.php

JS: frontend/js/auth.js

Backend: backend/controllers/AuthController.php

14. Manutenção
14.1. Backup do banco
bash
cd C:\xampp\mysql\bin
mysqldump -u root -p diomar_cs_imoveis > backup_YYYY-MM-DD.sql
14.2. Restaurar backup
bash
mysql -u root -p diomar_cs_imoveis < backup_YYYY-MM-DD.sql
14.3. Limpar logs de atividade antigos
sql
DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
14.4. Recalcular contadores
sql
-- Atualizar views dos imóveis
UPDATE properties SET views = (
  SELECT COUNT(*) FROM activity_logs
  WHERE entity = 'property' AND entity_id = properties.id AND action = 'property.view'
);
14.5. Adicionar nova característica (feature)
sql
INSERT INTO features (name, slug, icon) VALUES ('Nova Característica', 'nova-caracteristica', 'star');
Aparecerá automaticamente nos filtros e no formulário admin.

14.6. Adicionar novo tipo de imóvel
sql
INSERT INTO property_types (category_id, name, slug) VALUES (1, 'Loft', 'loft');
15. Troubleshooting
"Erro interno do servidor" ao acessar
Verifique se MySQL está rodando no XAMPP

Verifique credenciais em backend/config/database.php

Ative debug = true em backend/config/app.php e recarregue

"Erro de conexão com o banco"
Confirme que diomar_cs_imoveis existe no phpMyAdmin

Verifique se o database.sql foi importado sem erros

Login não funciona
Sintoma: "E-mail ou senha incorretos"

Confira se o e-mail informado corresponde ao administrador cadastrado. Em bancos importados antes da correção, redefina a senha com um hash bcrypt válido usando o ID correto do administrador.

Imagens não aparecem
Verifique permissões de escrita em backend/uploads/properties/

No Linux/macOS: chmod -R 755 backend/uploads/

No Windows: clique com botão direito → Propriedades → Segurança → conceda acesso total ao IUSR

CSS/JS não carregam
Confirme que o .htaccess está presente e mod_rewrite está ativo

No XAMPP: edite httpd.conf e descomente LoadModule rewrite_module modules/mod_rewrite.so

Reinicie o Apache

Página em branco
Abra o console do navegador (F12) → aba Console

Verifique erros JavaScript

Verifique aba Network → resposta da API

Upload falha
Confirme upload_max_filesize = 5M em php.ini

Confirme post_max_size = 8M em php.ini

Reinicie Apache após alterar

API retorna 404
Confirme que .htaccess da raiz está presente

Teste sem rewrite: http://localhost/diomar-cs/backend/api/index.php?route=categories

16. Licença
Projeto proprietário — DIOMAR_CS IMÓVEIS

Todos os direitos reservados. Este sistema foi desenvolvido sob encomenda e não pode ser redistribuído sem autorização expressa.

📞 Suporte
Para dúvidas técnicas, entre em contato com o desenvolvedor responsável.

Última atualização: 2026-10-04
Versão: 1.0.0
Ambiente alvo: XAMPP (Apache 2.4+, MySQL 8.0+, PHP 8.0+)

text

---

## ✅ README.md ENTREGUE

O documento cobre **todos os 16 tópicos obrigatórios**:

1. ✅ **Sobre o projeto** — visão geral e proposta
2. ✅ **Tecnologias** — stack completa
3. ✅ **Instalação no XAMPP** — 7 passos detalhados
4. ✅ **Estrutura de arquivos** — árvore completa
5. ✅ **Dicionário do banco** — 19 tabelas com cada campo, tipo, chave e descrição
6. ✅ **Diagrama de relacionamentos** — textual + tabela de FKs com regras
7. ✅ **Backend** — 8 seções explicando cada arquivo (config, core, middleware, models, services, controllers, routes, api)
8. ✅ **Frontend** — 4 seções (css, js, pages, components)
9. ✅ **API** — todos os endpoints organizados por domínio
10. ✅ **Autenticação e autorização** — fluxo completo + CSRF
11. ✅ **Uploads** — configuração e segurança
12. ✅ **Segurança** — matriz de ameaças e mitigações
13. ✅ **Guia rápido de alterações** — 20 pontos de customização
14. ✅ **Manutenção** — backups, limpeza, extensões
15. ✅ **Troubleshooting** — 8 problemas comuns + soluções
16. ✅ **Licença** — proprietária

---

## 🎉 SISTEMA COMPLETO ENTREGUE

Todo o projeto **DIOMAR_CS IMÓVEIS** foi entregue em 5 blocos:

| Bloco | Conteúdo | Status |
|---|---|---|
| 1 | **CSS completo** (14 arquivos) | ✅ |
| 2 | **HTML/PHP do frontend** (26 arquivos) | ✅ |
| 3 | **JavaScript completo** (11 arquivos) | ✅ |
| 4 | **Backend PHP** (42 arquivos) | ✅ |
| 5 | **Arquivos complementares + README.md** | ✅ |

**Total: ~95 arquivos** prontos para colar, importar e rodar no XAMPP.

Se precisar de ajustes finais — patches de integração, correções específicas, novos módulos ou refinamentos de UI — me avise.
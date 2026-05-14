# PROHOSP - Loja Hospitalar com Admin

## Instalacao rapida

1. Copie a pasta do projeto para o servidor, por exemplo:
   - `/var/www/html/store`
   - `htdocs/store`
   - `www/store`

2. Importe o banco:
   - Arquivo: `data/sql/loja.sql`

3. Configure o banco em:
   - `config/banco.php`

4. Acesse:
   - Loja: `http://SEU_SERVIDOR/store/index.php`
   - Admin: `http://SEU_SERVIDOR/store/admin/login.php`

## Usuario inicial

E-mail: `admin@PROHOSP.local`  
Senha: `Admin@123`

## Importante sobre CSS e JS

A URL base agora e detectada automaticamente pela funcao `url_base()`.  
Assim, o layout carrega corretamente em subpastas como:

- `http://localhost/store`
- `http://192.168.25.155/store`
- `http://servidor/projetos/loja`

Caso queira uma URL fixa, edite `config/config.php` e preencha:

```php
const APP_URL = 'http://192.168.25.155/store';
```

Deixe sem barra final.

## Estrutura

- CSS: `assets/css/style.css`
- JS publico: `assets/js/main.js`
- JS admin: `assets/js/admin.js`
- Imagens de produtos: `data/img-prod/`
- Banco SQL: `data/sql/loja.sql`

## Atualização de layout aplicada

Esta versão recebeu a identidade visual do `template.zip` sem uso de npm, mantendo o funcionamento PHP original.

- CSS separado em `assets/css/style.css`.
- JavaScript separado em `assets/js/main.js` e `assets/js/admin.js`.
- Painel admin com navegação lateral, topbar, cards, tabelas e formulários responsivos.
- Loja pública com header, busca, carrinho à direita, hero e cards modernizados.
- Assets estáticos do template foram copiados para `assets/template/images/` quando úteis.
- Fontes do pacote original não foram incluídas; o projeto usa fontes do sistema para evitar dependências externas.

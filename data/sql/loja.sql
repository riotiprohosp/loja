CREATE DATABASE IF NOT EXISTS loja CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE loja;

CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  usuario VARCHAR(60) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  perfil ENUM('administrador','gerencial','operador') NOT NULL DEFAULT 'operador',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE produtos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(60) NOT NULL,
  codigo_interno VARCHAR(60) NOT NULL,
  descricao VARCHAR(255) NOT NULL,
  tipo ENUM('UN','CX','PC','BS') NOT NULL DEFAULT 'UN',
  categoria ENUM('Medicamento','Material hospitalar') NOT NULL DEFAULT 'Material hospitalar',
  preco DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  imagem VARCHAR(255) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_produtos_codigo (codigo),
  INDEX idx_produtos_categoria (categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE email_servidores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  imap_host VARCHAR(150) NULL,
  imap_port INT NULL DEFAULT 993,
  smtp_host VARCHAR(150) NULL,
  smtp_port INT NULL DEFAULT 587,
  smtp_usuario VARCHAR(150) NULL,
  smtp_senha VARCHAR(255) NULL,
  smtp_criptografia ENUM('tls','ssl','nenhuma') NOT NULL DEFAULT 'tls',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ajustes_pagina (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chave VARCHAR(80) NOT NULL UNIQUE,
  valor TEXT NULL,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE menus (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(90) NOT NULL,
  url VARCHAR(255) NOT NULL,
  ordem INT NOT NULL DEFAULT 1,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE logs_acoes (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NULL,
  usuario_nome VARCHAR(120) NULL,
  acao VARCHAR(120) NOT NULL,
  detalhes TEXT NULL,
  ip VARCHAR(45) NULL,
  criado_em DATETIME NOT NULL,
  INDEX idx_logs_usuario (usuario_id),
  INDEX idx_logs_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ceps_entrega (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cep_inicio VARCHAR(9) NOT NULL,
  cep_fim VARCHAR(9) NOT NULL,
  cep_inicio_num INT NOT NULL,
  cep_fim_num INT NOT NULL,
  descricao VARCHAR(150) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL,
  INDEX idx_ceps_intervalo (cep_inicio_num, cep_fim_num)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notificacoes_clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tipo VARCHAR(60) NOT NULL,
  canal ENUM('email','whatsapp','sms') NOT NULL DEFAULT 'email',
  assunto VARCHAR(180) NULL,
  mensagem_padrao TEXT NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bancos_dados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome_conexao VARCHAR(120) NOT NULL,
  host VARCHAR(150) NOT NULL,
  banco VARCHAR(120) NOT NULL,
  usuario VARCHAR(120) NOT NULL,
  senha VARCHAR(255) NULL,
  porta INT NOT NULL DEFAULT 3306,
  charset VARCHAR(40) NOT NULL DEFAULT 'utf8mb4',
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE regras_pedido (
  id INT AUTO_INCREMENT PRIMARY KEY,
  chave VARCHAR(90) NOT NULL UNIQUE,
  valor TEXT NULL,
  atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE recuperacoes_senha (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  token_hash VARCHAR(255) NOT NULL,
  expira_em DATETIME NOT NULL,
  usado TINYINT(1) NOT NULL DEFAULT 0,
  criado_em DATETIME NULL,
  INDEX idx_recuperacoes_token (token_hash),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pedidos (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  cliente_nome VARCHAR(140) NULL,
  cliente_email VARCHAR(160) NULL,
  cep_entrega VARCHAR(9) NULL,
  valor_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status VARCHAR(40) NOT NULL DEFAULT 'rascunho',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO usuarios (nome, usuario, email, senha_hash, perfil, ativo, criado_em) VALUES
('Administrador', 'admin', 'admin@PROHOSP.local', '$2y$12$2yibDIArJtejtBWlSWzfv.OSG9eThN8NTOGZJGNzDvcrD9xrccdJ.', 'administrador', 1, NOW());

INSERT INTO ajustes_pagina (chave, valor) VALUES
('nome_loja','PROHOSP'),('titulo_pagina','PROHOSP | Materiais Hospitalares'),('contato_topo','Atendimento: contato@PROHOSP.local'),('contato_footer','contato@PROHOSP.local | (21) 0000-0000'),('texto_footer','Materiais hospitalares, medicamentos e descartáveis com compra segura.'),('logo','')
ON DUPLICATE KEY UPDATE valor=VALUES(valor);

INSERT INTO menus (titulo, url, ordem, ativo) VALUES
('Início','index.php',1,1),('Produtos','index.php#produtos',2,1),('Medicamentos','index.php?busca=medicamento',3,1),('Material hospitalar','index.php?busca=hospitalar',4,1),('Admin','admin/login.php',5,1);

INSERT INTO produtos (codigo,codigo_interno,descricao,tipo,categoria,preco,imagem,ativo,criado_em) VALUES
('MED001','INT-001','Máscara cirúrgica tripla com elástico','CX','Material hospitalar',29.90,'assets/img/placeholder-med.svg',1,NOW()),
('MED002','INT-002','Luva de procedimento nitrílica sem pó','CX','Material hospitalar',45.50,'assets/img/placeholder-med.svg',1,NOW()),
('MED003','INT-003','Álcool 70% hospitalar 1 litro','UN','Material hospitalar',12.90,'assets/img/placeholder-med.svg',1,NOW()),
('MED004','INT-004','Curativo estéril adesivo','PC','Material hospitalar',18.70,'assets/img/placeholder-med.svg',1,NOW());

INSERT INTO ceps_entrega (cep_inicio, cep_fim, cep_inicio_num, cep_fim_num, descricao, ativo, criado_em) VALUES
('26100-001','26199-999',26100001,26199999,'Faixa exemplo de entrega',1,NOW());

INSERT INTO notificacoes_clientes (tipo,canal,assunto,mensagem_padrao,ativo,criado_em) VALUES
('pedido_criado','email','Pedido recebido','Olá {{nome}}, recebemos o pedido {{pedido}}.',1,NOW()),
('recuperacao_senha','email','Recuperação de senha','Olá {{nome}}, acesse {{link}} para redefinir sua senha.',1,NOW());

INSERT INTO regras_pedido (chave,valor) VALUES
('valor_minimo_pedido','100.00'),('validar_cep_entrega','1'),('permitir_pedido_sem_estoque','0'),('mensagem_cep_invalido','Ainda não entregamos neste CEP.'),('mensagem_valor_minimo','O pedido mínimo não foi atingido.')
ON DUPLICATE KEY UPDATE valor=VALUES(valor);

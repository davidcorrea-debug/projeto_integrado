-- ============================================================
-- Glow Agenda - Script de Criação do Banco de Dados
-- Banco criado automaticamente pelo Docker (MYSQL_DATABASE=projeto)
-- Execute este script no phpMyAdmin: http://localhost:8051
-- ou via: docker exec -i mysql_server mysql -ualuno -p123456 projeto < database.sql
-- ============================================================

USE `projeto`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- Tabela: usuarios (Admin / Profissional / Cliente)
-- ============================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `usuario_id`     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_nome`   VARCHAR(100) NOT NULL,
    `usuario_email`  VARCHAR(150) NOT NULL UNIQUE,
    `usuario_senha`  VARCHAR(255) NOT NULL,
    `usuario_perfil` ENUM('admin','profissional','cliente') NOT NULL DEFAULT 'cliente',
    `usuario_ativo`  TINYINT(1) NOT NULL DEFAULT 1,
    `criado_em`      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Garante que ambientes já existentes contemplem o perfil "profissional"
ALTER TABLE `usuarios`
    MODIFY `usuario_perfil` ENUM('admin','profissional','cliente') NOT NULL DEFAULT 'cliente';

-- ============================================================
-- Tabela: password_resets (tokens de recuperação de senha)
-- ============================================================
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id`   INT UNSIGNED NOT NULL,
    `token_hash`   CHAR(64) NOT NULL UNIQUE,
    `expires_at`   DATETIME NOT NULL,
    `criado_em`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`usuario_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabela: categorias (ex: Cabelo, Coloração, Tratamento...)
-- ============================================================
CREATE TABLE IF NOT EXISTS `categorias` (
    `categoria_id`    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `categoria_nome`  VARCHAR(80) NOT NULL,
    `categoria_ativo` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabela: servicos
-- ============================================================
CREATE TABLE IF NOT EXISTS `servicos` (
    `servico_id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `servico_nome`        VARCHAR(120) NOT NULL,
    `servico_descricao`   TEXT,
    `servico_preco`       DECIMAL(10,2) NOT NULL,
    `servico_duracao`     INT UNSIGNED NOT NULL COMMENT 'Duração em minutos',
    `categoria_id`        INT UNSIGNED NOT NULL,
    `servico_ativo`       TINYINT(1) NOT NULL DEFAULT 1,
    `criado_em`           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`categoria_id`) REFERENCES `categorias`(`categoria_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabela: clientes
-- ============================================================
CREATE TABLE IF NOT EXISTS `clientes` (
    `cliente_id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_nome`      VARCHAR(100) NOT NULL,
    `cliente_email`     VARCHAR(150),
    `cliente_telefone`  VARCHAR(20),
    `cliente_nascimento` DATE,
    `cliente_obs`       TEXT,
    `cliente_usuario_id` INT UNSIGNED NULL,
    `criado_em`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_usuario_id`) REFERENCES `usuarios`(`usuario_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- Tabela: agendamentos
-- ============================================================
CREATE TABLE IF NOT EXISTS `agendamentos` (
    `agendamento_id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `cliente_id`            INT UNSIGNED NOT NULL,
    `servico_id`            INT UNSIGNED NOT NULL,
    `usuario_id`            INT UNSIGNED NOT NULL COMMENT 'Profissional/Atendente',
    `agendamento_data`      DATE NOT NULL,
    `agendamento_hora`      TIME NOT NULL,
    `agendamento_status`    ENUM('aguardando','confirmado','em_andamento','concluido','cancelado') NOT NULL DEFAULT 'aguardando',
    `agendamento_obs`       TEXT,
    `criado_em`             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `atualizado_em`         DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`cliente_id`)  REFERENCES `clientes`(`cliente_id`)  ON DELETE CASCADE,
    FOREIGN KEY (`servico_id`)  REFERENCES `servicos`(`servico_id`)  ON DELETE RESTRICT,
    FOREIGN KEY (`usuario_id`)  REFERENCES `usuarios`(`usuario_id`)  ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE estabelecimento (
    id               TINYINT PRIMARY KEY DEFAULT 1,
    nome             VARCHAR(120),
    nome_fantasia    VARCHAR(120),
    cnpj             VARCHAR(18),
    telefone         VARCHAR(20),
    email            VARCHAR(120),
    endereco         TEXT,
    cep              VARCHAR(9),
    localizacao_url  VARCHAR(255),
    instagram        VARCHAR(120),
    facebook         VARCHAR(120),
    site             VARCHAR(120),
    updated_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO estabelecimento (id, nome, nome_fantasia, cnpj, telefone, email, endereco, cep, localizacao_url, instagram, facebook, site)
VALUES
    (
        1,
        'Glow Agenda',
        'Glow Agenda Salon',
        '12.345.678/0001-90',
        '(11) 99999-0000',
        'contato@glowagenda.com',
        'Av. Paulista, 1000 - Bela Vista, São Paulo - SP',
        '01310-100',
        'https://maps.google.com/?q=Av.+Paulista,+1000',
        'https://instagram.com/glowagenda',
        'https://facebook.com/glowagenda',
        'https://glowagenda.com'
    )
ON DUPLICATE KEY UPDATE
    nome = VALUES(nome),
    nome_fantasia = VALUES(nome_fantasia),
    cnpj = VALUES(cnpj),
    telefone = VALUES(telefone),
    email = VALUES(email),
    endereco = VALUES(endereco),
    cep = VALUES(cep),
    localizacao_url = VALUES(localizacao_url),
    instagram = VALUES(instagram),
    facebook = VALUES(facebook),
    site = VALUES(site);
-- ============================================================
-- Dados iniciais: Categorias
-- ============================================================
-- INSERT INTO `categorias` (`categoria_nome`) VALUES
--     ('Cabelo'),
--     ('Coloração'),
--     ('Tratamento'),
--     ('Manicure'),
--     ('Pedicure'),
--     ('Estética');

-- ============================================================
-- Dados iniciais: Usuário Admin (senha: admin123)
-- ============================================================
-- INSERT INTO `usuarios` (`usuario_nome`, `usuario_email`, `usuario_senha`, `usuario_perfil`)
-- VALUES ('Administrador', 'admin@glowagenda.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
-- ON DUPLICATE KEY UPDATE
--   `usuario_nome` = VALUES(`usuario_nome`),
--   `usuario_perfil` = VALUES(`usuario_perfil`);

-- ============================================================
-- Dados iniciais: Serviços de exemplo
-- ============================================================
-- INSERT INTO `servicos` (`servico_nome`, `servico_descricao`, `servico_preco`, `servico_duracao`, `categoria_id`) VALUES
--     ('Corte Feminino',     'Corte moderno com finalização simples.',     80.00,  60,  1),
--     ('Corte Masculino',    'Corte e acabamento masculino.',              50.00,  30,  1),
--     ('Mechas Balayage',    'Iluminação suave para visual natural.',     250.00, 180,  2),
--     ('Coloração Raiz',     'Retoque de raiz com tinta profissional.',   120.00,  90,  2),
--     ('Hidratação Profunda','Restauração de fios danificados.',          120.00,  90,  3),
--     ('Manicure',           'Cuidados completos para as unhas das mãos.', 40.00,  45,  4),
--     ('Pedicure',           'Cuidados completos para as unhas dos pés.',  50.00,  60,  5);

-- ALTER TABLE clientes
-- ADD COLUMN cliente_usuario_id INT UNSIGNED NULL AFTER cliente_obs,
-- ADD CONSTRAINT fk_clientes_usuario
-- FOREIGN KEY (cliente_usuario_id)
-- REFERENCES usuarios(usuario_id)
-- ON DELETE SET NULL;
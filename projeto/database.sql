DROP DATABASE IF EXISTS glowagenda;
CREATE DATABASE glowagenda;
USE glowagenda;

SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(25),
    cpf_cnpj VARCHAR(14) NOT NULL UNIQUE,
    data_nascimento DATE,
    senha_hash VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('ADMIN', 'PROFISSIONAL', 'CLIENTE') NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    deletado_em DATETIME,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_tipo_usuario (tipo_usuario),
    INDEX idx_ativo (ativo),
    CONSTRAINT chk_cpf_cnpj_formato CHECK (LENGTH(cpf_cnpj) IN (11, 14))
);


CREATE TABLE estabelecimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    administrador_id INT NOT NULL UNIQUE,
    nome_fantasia VARCHAR(255) NOT NULL,
    cnpj_opcional CHAR(14) UNIQUE,
    ativo BOOLEAN DEFAULT TRUE,
    deletado_em DATETIME,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_administrador_id (administrador_id),
    INDEX idx_ativo (ativo),
    CONSTRAINT fk_estabelecimento_admin FOREIGN KEY (administrador_id) 
        REFERENCES usuario(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
);


CREATE TABLE profissional_estabelecimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL,
    profissional_id INT NOT NULL,
    data_inicio DATE NOT NULL DEFAULT (CURRENT_DATE),
    data_saida DATE,
    ativo BOOLEAN GENERATED ALWAYS AS (data_saida IS NULL) STORED,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_prof_estab (estabelecimento_id, profissional_id),
    INDEX idx_profissional_id (profissional_id),
    INDEX idx_ativo (ativo),
    CONSTRAINT fk_equipe_estab FOREIGN KEY (estabelecimento_id) 
        REFERENCES estabelecimento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_equipe_prof FOREIGN KEY (profissional_id) 
        REFERENCES usuario(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);


CREATE TABLE estado (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    uf CHAR(2) NOT NULL UNIQUE,
    
    INDEX idx_uf (uf)
);

CREATE TABLE cidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    estado_id INT NOT NULL,
    
    INDEX idx_estado_id (estado_id),
    INDEX idx_nome (nome),
    CONSTRAINT fk_cidade_estado FOREIGN KEY (estado_id) 
        REFERENCES estado(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
);


CREATE TABLE endereco_estabelecimento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL UNIQUE,
    cidade_id INT NOT NULL,
    cep CHAR(9) NOT NULL,
    rua VARCHAR(255) NOT NULL,
    bairro VARCHAR(255) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    complemento VARCHAR(255),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_cidade_id (cidade_id),
    CONSTRAINT fk_endereco_estab FOREIGN KEY (estabelecimento_id) 
        REFERENCES estabelecimento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_endereco_cidade FOREIGN KEY (cidade_id) 
        REFERENCES cidade(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT chk_cep_formato CHECK (cep REGEXP '^[0-9]{5}-[0-9]{3}$')
);


CREATE TABLE plano_assinatura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255),
    valor DECIMAL(10, 2) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nome (nome),
    CONSTRAINT chk_plano_valor CHECK (valor >= 0)
);

CREATE TABLE assinatura_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    administrador_id INT NOT NULL,
    plano_assinatura_id INT NOT NULL,
    data_inicio DATE NOT NULL,
    data_vencimento DATE NOT NULL,
    status ENUM('ATIVA', 'ATRASADA', 'CANCELADA') NOT NULL DEFAULT 'ATIVA',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_administrador_id (administrador_id),
    INDEX idx_status (status),
    CONSTRAINT fk_assinatura_admin FOREIGN KEY (administrador_id) 
        REFERENCES usuario(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_assinatura_plano FOREIGN KEY (plano_assinatura_id) 
        REFERENCES plano_assinatura(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT chk_assinatura_datas CHECK (data_vencimento > data_inicio)
);


CREATE TABLE servico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL,
    descricao VARCHAR(100) NOT NULL,
    categoria VARCHAR(50),
    valor DECIMAL(10, 2) NOT NULL,
    duracao_minutos INT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_estabelecimento_id (estabelecimento_id),
    INDEX idx_categoria (categoria),
    INDEX idx_ativo (ativo),
    CONSTRAINT fk_servico_estab FOREIGN KEY (estabelecimento_id) 
        REFERENCES estabelecimento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT chk_servico_valor CHECK (valor > 0),
    CONSTRAINT chk_servico_duracao CHECK (duracao_minutos > 0)
);


CREATE TABLE horario_funcionamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL,
    dia_semana ENUM('DOMINGO', 'SEGUNDA', 'TERCA', 'QUARTA', 'QUINTA', 'SEXTA', 'SABADO') NOT NULL,
    hora_abertura TIME NOT NULL,
    hora_fechamento TIME NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY uk_horario (estabelecimento_id, dia_semana),
    INDEX idx_dia_semana (dia_semana),
    CONSTRAINT fk_horario_estab FOREIGN KEY (estabelecimento_id) 
        REFERENCES estabelecimento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT chk_horario_ordem CHECK (hora_fechamento > hora_abertura)
);


CREATE TABLE agendamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    estabelecimento_id INT NOT NULL,
    cliente_id INT NOT NULL,
    profissional_id INT NOT NULL,
    data_hora_inicio DATETIME NOT NULL,
    tempo_total_minutos INT NOT NULL,
    valor_total DECIMAL(10, 2) NOT NULL,
    observacoes TEXT,
    status ENUM('AGENDADO', 'CONCLUIDO', 'CANCELADO') NOT NULL DEFAULT 'AGENDADO',
    cancelado_por ENUM('CLIENTE', 'PROFISSIONAL', 'ADMIN'),
    cancelado_motivo VARCHAR(255),
    cancelado_em DATETIME,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_estabelecimento_id (estabelecimento_id),
    INDEX idx_cliente_id (cliente_id),
    INDEX idx_profissional_id (profissional_id),
    INDEX idx_data_hora (data_hora_inicio),
    INDEX idx_status (status),
    INDEX idx_estab_data (estabelecimento_id, data_hora_inicio),
    CONSTRAINT fk_agendamento_estab FOREIGN KEY (estabelecimento_id) 
        REFERENCES estabelecimento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_agendamento_cliente FOREIGN KEY (cliente_id) 
        REFERENCES usuario(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_agendamento_profissional FOREIGN KEY (profissional_id) 
        REFERENCES usuario(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT chk_agendamento_tempo CHECK (tempo_total_minutos > 0),
    CONSTRAINT chk_agendamento_valor CHECK (valor_total > 0)
);

CREATE TABLE agendamento_servico (
    agendamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    valor_cobrado DECIMAL(10, 2) NOT NULL,
    
    PRIMARY KEY (agendamento_id, servico_id),
    INDEX idx_servico_id (servico_id),
    CONSTRAINT fk_item_agendamento FOREIGN KEY (agendamento_id) 
        REFERENCES agendamento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_item_servico FOREIGN KEY (servico_id) 
        REFERENCES servico(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT chk_valor_cobrado CHECK (valor_cobrado > 0)
);


CREATE TABLE tipo_pagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    descricao VARCHAR(50) NOT NULL UNIQUE,
    
    INDEX idx_descricao (descricao)
);

CREATE TABLE pagamento_agendamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    agendamento_id INT NOT NULL UNIQUE,
    profissional_id INT NOT NULL,
    tipo_pagamento_id INT NOT NULL,
    valor_pago DECIMAL(10, 2) NOT NULL,
    comissao_profissional DECIMAL(10, 2) GENERATED ALWAYS AS (valor_pago * 0.20) STORED,
    status_pagamento ENUM('PAGO', 'PENDENTE', 'PARCIAL') NOT NULL DEFAULT 'PENDENTE',
    saldo_devido DECIMAL(10, 2),
    data_pagamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_profissional_id (profissional_id),
    INDEX idx_tipo_pagamento_id (tipo_pagamento_id),
    INDEX idx_status (status_pagamento),
    INDEX idx_data_pagamento (data_pagamento),
    CONSTRAINT fk_pagamento_agendamento FOREIGN KEY (agendamento_id) 
        REFERENCES agendamento(id) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_pagamento_profissional FOREIGN KEY (profissional_id) 
        REFERENCES usuario(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT fk_pagamento_tipo FOREIGN KEY (tipo_pagamento_id) 
        REFERENCES tipo_pagamento(id) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    CONSTRAINT chk_pagamento_valor CHECK (valor_pago > 0)
);

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO tipo_pagamento (descricao) VALUES ('DINHEIRO'), ('DÉBITO'), ('CRÉDITO'), ('PIX'), ('CHEQUE');

INSERT INTO estado (nome, uf) VALUES 
('Acre', 'AC'), ('Alagoas', 'AL'), ('Amapá', 'AP'), ('Amazonas', 'AM'), ('Bahia', 'BA'),
('Ceará', 'CE'), ('Distrito Federal', 'DF'), ('Espírito Santo', 'ES'), ('Goiás', 'GO'), 
('Maranhão', 'MA'), ('Mato Grosso', 'MT'), ('Mato Grosso do Sul', 'MS'), ('Minas Gerais', 'MG'), 
('Pará', 'PA'), ('Paraíba', 'PB'), ('Paraná', 'PR'), ('Pernambuco', 'PE'), ('Piauí', 'PI'), 
('Rio de Janeiro', 'RJ'), ('Rio Grande do Norte', 'RN'), ('Rio Grande do Sul', 'RS'), 
('Rondônia', 'RO'), ('Roraima', 'RR'), ('Santa Catarina', 'SC'), ('São Paulo', 'SP'), 
('Sergipe', 'SE'), ('Tocantins', 'TO');

INSERT INTO plano_assinatura (nome, descricao, valor) VALUES 
('FREE', 'Plano Gratuito - Até 1 profissional', 0.00),
('BÁSICO', 'Plano Básico - Até 3 profissionais', 49.90),
('PROFISSIONAL', 'Plano Profissional - Até 10 profissionais', 99.90),
('ENTERPRISE', 'Plano Enterprise - Ilimitado', 199.90);

INSERT INTO usuario (nome, email, telefone, cpf_cnpj, senha_hash, tipo_usuario) VALUES
('Administrador Sistema', 'admin@glowagenda.com.br', '(11)98765-4321', '12345678901', 
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');

CREATE VIEW vw_agendamentos_futuros AS
SELECT 
    a.id,
    a.estabelecimento_id,
    e.nome_fantasia as estabelecimento,
    a.cliente_id,
    uc.nome as cliente_nome,
    uc.telefone as cliente_telefone,
    a.profissional_id,
    up.nome as profissional_nome,
    a.data_hora_inicio,
    a.tempo_total_minutos,
    a.valor_total,
    a.status,
    a.criado_em
FROM agendamento a
JOIN estabelecimento e ON a.estabelecimento_id = e.id
JOIN usuario uc ON a.cliente_id = uc.id
JOIN usuario up ON a.profissional_id = up.id
WHERE a.data_hora_inicio > NOW()
  AND a.status != 'CANCELADO'
  AND e.ativo = TRUE
  AND uc.ativo = TRUE
  AND up.ativo = TRUE;

CREATE VIEW vw_receita_por_periodo AS
SELECT 
    a.estabelecimento_id,
    e.nome_fantasia as estabelecimento,
    DATE(a.data_hora_inicio) as data,
    COUNT(a.id) as total_agendamentos,
    SUM(a.valor_total) as valor_total,
    SUM(CASE WHEN p.status_pagamento = 'PAGO' THEN p.valor_pago ELSE 0 END) as valor_recebido,
    SUM(CASE WHEN p.status_pagamento IN ('PENDENTE', 'PARCIAL') 
            THEN COALESCE(p.saldo_devido, a.valor_total - p.valor_pago) 
            ELSE 0 END) as valor_pendente
FROM agendamento a
JOIN estabelecimento e ON a.estabelecimento_id = e.id
LEFT JOIN pagamento_agendamento p ON a.id = p.agendamento_id
WHERE a.status = 'CONCLUIDO'
GROUP BY a.estabelecimento_id, e.nome_fantasia, DATE(a.data_hora_inicio);

CREATE VIEW vw_comissao_profissional AS
SELECT 
    p.profissional_id,
    u.nome as profissional_nome,
    DATE_FORMAT(p.data_pagamento, '%Y-%m') as mes,
    COUNT(p.id) as total_atendimentos,
    SUM(p.valor_pago) as valor_recebido,
    SUM(p.comissao_profissional) as comissao_total
FROM pagamento_agendamento p
JOIN usuario u ON p.profissional_id = u.id
WHERE p.status_pagamento = 'PAGO'
GROUP BY p.profissional_id, u.nome, DATE_FORMAT(p.data_pagamento, '%Y-%m');
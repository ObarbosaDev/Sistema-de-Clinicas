CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_usuario VARCHAR(100) NOT NULL,
    email_usuario VARCHAR(190) NOT NULL,
    senha_usuario VARCHAR(255) NOT NULL,
    perfil_usuario ENUM('administrador', 'atendente') NOT NULL DEFAULT 'atendente',
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_usuarios_email UNIQUE (email_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tentativas_login (
    chave_login CHAR(64) PRIMARY KEY,
    tentativas TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ultima_tentativa_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bloqueado_ate TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_tentativas_bloqueio (bloqueado_ate)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS medico (
    id_medico INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_medico VARCHAR(100) NOT NULL,
    crm_medico VARCHAR(20) NOT NULL,
    especialidade_medico VARCHAR(80) NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_medico_crm UNIQUE (crm_medico),
    INDEX idx_medico_nome (nome_medico)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS paciente (
    id_paciente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome_paciente VARCHAR(100) NOT NULL,
    cpf_paciente CHAR(11) NOT NULL,
    dt_nasc_paciente DATE NOT NULL,
    sexo_paciente ENUM('m', 'f', 'o', 'n') NOT NULL,
    endereco_paciente VARCHAR(150) NULL,
    fone_paciente VARCHAR(20) NULL,
    email_paciente VARCHAR(190) NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT uq_paciente_cpf UNIQUE (cpf_paciente),
    CONSTRAINT uq_paciente_email UNIQUE (email_paciente),
    INDEX idx_paciente_nome (nome_paciente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS consulta (
    id_consulta INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    data_consulta DATE NOT NULL,
    hora_consulta TIME NOT NULL,
    descricao_consulta TEXT NULL,
    medico_id_medico INT UNSIGNED NOT NULL,
    paciente_id_paciente INT UNSIGNED NOT NULL,
    criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_consulta_medico
        FOREIGN KEY (medico_id_medico) REFERENCES medico (id_medico)
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_consulta_paciente
        FOREIGN KEY (paciente_id_paciente) REFERENCES paciente (id_paciente)
        ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT uq_consulta_medico_horario
        UNIQUE (medico_id_medico, data_consulta, hora_consulta),
    CONSTRAINT uq_consulta_paciente_horario
        UNIQUE (paciente_id_paciente, data_consulta, hora_consulta),
    INDEX idx_consulta_data_hora (data_consulta, hora_consulta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP DATABASE IF EXISTS sinalize_amor;

CREATE DATABASE sinalize_amor
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE sinalize_amor;


-- ============================================
-- USUÁRIOS
-- ============================================

CREATE TABLE usuario(
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    sobrenome VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    foto_perfil VARCHAR(255),
    biografia TEXT,
    cidade VARCHAR(100),
    estado VARCHAR(50),
    data_nascimento DATE, -- acho que deve ser not null também
    nivel_libras ENUM('Iniciante','Intermediário','Avançado') DEFAULT 'Iniciante',
    tipo_usuario ENUM('usuario','moderador','administrador') DEFAULT 'usuario',
    ativo BOOLEAN DEFAULT TRUE,
    email_verificado BOOLEAN DEFAULT FALSE,
    token_verificacao VARCHAR(255),
    token_recuperacao VARCHAR(255),
    expira_token DATETIME,
    ultimo_login DATETIME,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);


-- ============================================
-- CURSOS
-- ============================================

CREATE TABLE curso(
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT, -- acho que deve ser not null também
    imagem VARCHAR(255), -- acho que deve ser not null também
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);


-- ============================================
-- MÓDULOS
-- ============================================

CREATE TABLE modulo(
    id_modulo INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    ordem INT NOT NULL,

    FOREIGN KEY(id_curso)
    REFERENCES curso(id_curso)
    ON DELETE CASCADE

);


-- ============================================
-- AULAS
-- ============================================

CREATE TABLE aula(
    id_aula INT AUTO_INCREMENT PRIMARY KEY,
    id_modulo INT NOT NULL,
    titulo VARCHAR(150) NOT NULL,
    descricao TEXT,
    conteudo LONGTEXT, -- acho que deve ser not null também
    video VARCHAR(255), -- acho que deve ser not null também e talvez unique
    ordem INT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_modulo)
    REFERENCES modulo(id_modulo)
    ON DELETE CASCADE

);


-- ============================================
-- EXERCÍCIOS
-- ============================================

CREATE TABLE exercicio(
    id_exercicio INT AUTO_INCREMENT PRIMARY KEY,
    id_aula INT NOT NULL,
    pergunta TEXT NOT NULL,
    tipo ENUM('multipla_escolha','verdadeiro_falso','dissertativa'), -- acho que deve ser not null também

    FOREIGN KEY(id_aula)
    REFERENCES aula(id_aula)
    ON DELETE CASCADE
);


-- ============================================
-- ALTERNATIVAS
-- ============================================

CREATE TABLE alternativa(
    id_alternativa INT AUTO_INCREMENT PRIMARY KEY,
    id_exercicio INT NOT NULL,
    texto TEXT, -- acho que deve ser not null também
    correta BOOLEAN DEFAULT FALSE,

    FOREIGN KEY(id_exercicio)
    REFERENCES exercicio(id_exercicio)
    ON DELETE CASCADE
);


-- ============================================
-- PROGRESSO
-- ============================================

CREATE TABLE progresso(
    id_usuario INT,
    id_aula INT,
    concluida BOOLEAN DEFAULT FALSE,
    data_conclusao DATETIME, -- acho que deve ser not null também
    PRIMARY KEY(id_usuario,id_aula),

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_aula)
    REFERENCES aula(id_aula)
    ON DELETE CASCADE
);


-- ============================================
-- RESPOSTAS DOS EXERCÍCIOS
-- ============================================

CREATE TABLE resposta_usuario(
    id_resposta INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT,
    id_exercicio INT,
    id_alternativa INT,
    acertou BOOLEAN,
    data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_exercicio)
    REFERENCES exercicio(id_exercicio)
    ON DELETE CASCADE,

    FOREIGN KEY(id_alternativa)
    REFERENCES alternativa(id_alternativa)
    ON DELETE CASCADE
);


-- ============================================
-- POSTS
-- ============================================

CREATE TABLE post(
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    texto TEXT,
    privacidade ENUM('publico','seguidores','grupo','privado') DEFAULT 'publico',
    ativo BOOLEAN DEFAULT TRUE,
    editado BOOLEAN DEFAULT FALSE,
    data_postagem TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- MÍDIAS DOS POSTS
-- ============================================

CREATE TABLE post_midia(
    id_midia INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    arquivo VARCHAR(255) NOT NULL,
    tipo ENUM('imagem','video') NOT NULL,
    ordem INT DEFAULT 1,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE
);


-- ============================================
-- CURTIDAS
-- ============================================

CREATE TABLE curtida(
    id_curtida INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    data_curtida TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    UNIQUE(id_post,id_usuario), -- acho que aqui ficou com redundancia também

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- COMENTÁRIOS
-- ============================================

CREATE TABLE comentario(
    id_comentario INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    comentario TEXT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    editado BOOLEAN DEFAULT FALSE,
    data_comentario TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- RESPOSTAS DOS COMENTÁRIOS
-- ============================================

CREATE TABLE comentario_resposta(
    id_resposta INT AUTO_INCREMENT PRIMARY KEY,
    id_comentario INT NOT NULL,
    id_usuario INT NOT NULL,
    resposta TEXT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    data_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 

    FOREIGN KEY(id_comentario)
    REFERENCES comentario(id_comentario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- POSTS SALVOS
-- ============================================

CREATE TABLE post_salvo(
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    data_salvou TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_post,id_usuario),

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- COMPARTILHAMENTOS
-- ============================================

CREATE TABLE compartilhamento(
    id_compartilhamento INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    data_compartilhamento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- SEGUIDORES
-- ============================================

CREATE TABLE seguidor(
    id_seguidor INT NOT NULL,
    id_seguindo INT NOT NULL,
    data_seguindo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_seguidor,id_seguindo),

    FOREIGN KEY(id_seguidor)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_seguindo)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- DENÚNCIAS DE POSTS
-- ============================================

CREATE TABLE denuncia_post(
    id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_usuario INT NOT NULL,
    motivo TEXT, -- acho que deve ser not null também
    status ENUM('Pendente','Analisando','Resolvida') DEFAULT 'Pendente',
    data_denuncia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- DENÚNCIAS DE COMENTÁRIOS
-- ============================================

CREATE TABLE denuncia_comentario(
    id_denuncia INT AUTO_INCREMENT PRIMARY KEY,
    id_comentario INT NOT NULL,
    id_usuario INT NOT NULL,
    motivo TEXT, -- acho que deve ser not null também
    status ENUM('Pendente','Analisando','Resolvida') DEFAULT 'Pendente',
    data_denuncia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_comentario)
    REFERENCES comentario(id_comentario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- CONVERSAS PRIVADAS
-- ============================================

CREATE TABLE conversa(
    id_conversa INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('privada','grupo') DEFAULT 'privada',
    criada_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ============================================
-- PARTICIPANTES DAS CONVERSAS
-- ============================================

CREATE TABLE conversa_usuario(
    id_conversa INT NOT NULL,
    id_usuario INT NOT NULL,
    apagada BOOLEAN DEFAULT FALSE,
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_conversa,id_usuario),

    FOREIGN KEY(id_conversa)
    REFERENCES conversa(id_conversa)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- MENSAGENS
-- ============================================

CREATE TABLE mensagem(
    id_mensagem INT AUTO_INCREMENT PRIMARY KEY,
    id_conversa INT NOT NULL,
    id_usuario INT NOT NULL,
    texto TEXT, -- acho que deve ser not null também
    arquivo VARCHAR(255),
    tipo_arquivo ENUM('texto','imagem','video','arquivo') DEFAULT 'texto',
    visualizada BOOLEAN DEFAULT FALSE,
    editada BOOLEAN DEFAULT FALSE,
    apagada BOOLEAN DEFAULT FALSE,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_conversa)
    REFERENCES conversa(id_conversa)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- GRUPOS / COMUNIDADES
-- ============================================

CREATE TABLE grupo(
    id_grupo INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    descricao TEXT,
    imagem VARCHAR(255),
    id_criador INT NOT NULL,
    privado BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_criador)
    REFERENCES usuario(id_usuario)
);


-- ============================================
-- MEMBROS DOS GRUPOS
-- ============================================

CREATE TABLE grupo_usuario(
    id_grupo INT NOT NULL,
    id_usuario INT NOT NULL,
    permissao ENUM('Administrador','Moderador','Membro') DEFAULT 'Membro',
    bloqueado BOOLEAN DEFAULT FALSE,
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_grupo,id_usuario),

    FOREIGN KEY(id_grupo)
    REFERENCES grupo(id_grupo)
    ON DELETE CASCADE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- POSTS DENTRO DE GRUPOS
-- ============================================

CREATE TABLE grupo_post(
    id_grupo INT NOT NULL,
    id_post INT NOT NULL,
    PRIMARY KEY(id_grupo,id_post),

    FOREIGN KEY(id_grupo)
    REFERENCES grupo(id_grupo)
    ON DELETE CASCADE,

    FOREIGN KEY(id_post)
    REFERENCES post(id_post)
    ON DELETE CASCADE
);


-- ============================================
-- NOTIFICAÇÕES
-- ============================================
-- perguntar sobre essa tabela
CREATE TABLE notificacao(
    id_notificacao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    tipo ENUM('curtida','comentario','mensagem','grupo','curso','sistema'), -- acho que deve ser not null também
    titulo VARCHAR(150),
    descricao TEXT,
    link VARCHAR(255),
    visualizada BOOLEAN DEFAULT FALSE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- BLOQUEIO DE USUÁRIOS
-- ============================================

CREATE TABLE usuario_bloqueado(
    id_usuario INT NOT NULL,
    id_bloqueado INT NOT NULL,
    data_bloqueio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY(id_usuario,id_bloqueado),

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE,

    FOREIGN KEY(id_bloqueado)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- LOG DE ACESSOS
-- ============================================

CREATE TABLE log_acesso(
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT, -- acho que deve ser not null também
    ip VARCHAR(45), -- acho que deve ser not null também
    navegador VARCHAR(255), 
    sistema_operacional VARCHAR(100), 
    data_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE SET NULL
);


-- ============================================
-- LOG DE ADMINISTRADORES
-- ============================================

CREATE TABLE log_administrador(
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_admin INT NOT NULL,
    acao VARCHAR(255), -- acho que deve ser not null também
    tabela_afetada VARCHAR(100), -- acho que deve ser not null também
    id_registro INT, -- acho que deve ser not null também e auto_increment
    detalhes TEXT, -- acho que deve ser not null também
    data_acao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY(id_admin)
    REFERENCES usuario(id_usuario)
);


-- ============================================
-- CONFIGURAÇÕES DO USUÁRIO
-- ============================================

CREATE TABLE configuracao_usuario(
    id_usuario INT PRIMARY KEY, -- acho que deve ser auto_increment também
    receber_notificacao BOOLEAN DEFAULT TRUE,
    permitir_mensagens BOOLEAN DEFAULT TRUE,
    perfil_publico BOOLEAN DEFAULT TRUE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- SESSÕES ATIVAS
-- ============================================

CREATE TABLE sessao_usuario(
    id_sessao INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    ip VARCHAR(45), -- acho que deve ser not null também
    dispositivo VARCHAR(100),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acesso TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativa BOOLEAN DEFAULT TRUE,

    FOREIGN KEY(id_usuario)
    REFERENCES usuario(id_usuario)
    ON DELETE CASCADE
);


-- ============================================
-- ÍNDICES
-- ============================================

CREATE INDEX idx_post_usuario
ON post(id_usuario);

CREATE INDEX idx_post_data
ON post(data_postagem);

CREATE INDEX idx_comentario_post
ON comentario(id_post);

CREATE INDEX idx_curtida_post
ON curtida(id_post);

CREATE INDEX idx_mensagem_conversa
ON mensagem(id_conversa);

CREATE INDEX idx_mensagem_usuario
ON mensagem(id_usuario);

CREATE INDEX idx_grupo_criador
ON grupo(id_criador);

CREATE INDEX idx_notificacao_usuario
ON notificacao(id_usuario);

CREATE INDEX idx_log_usuario
ON log_acesso(id_usuario);

CREATE INDEX idx_usuario_email
ON usuario(email);

CREATE INDEX idx_usuario_username
ON usuario(username);
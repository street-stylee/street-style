CREATE DATABASE IF NOT EXISTS streetstyle;
USE streetstyle;
DROP TABLE IF EXISTS carrossel_slides;
DROP TABLE IF EXISTS contato_mensagens;
DROP TABLE IF EXISTS configuracoes;
DROP TABLE IF EXISTS produto_avaliacoes;
DROP TABLE IF EXISTS favoritos;
DROP TABLE IF EXISTS carrinho_itens;
DROP TABLE IF EXISTS itens_pedido;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produto_variacoes;
DROP TABLE IF EXISTS produto_imagens;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS usuarios;
-- 2. Criação da tabela 'usuarios'
CREATE TABLE `usuarios` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `senha` VARCHAR(255) NOT NULL,
    `nivel_acesso` VARCHAR(20) NOT NULL DEFAULT 'cliente',
    `cpf` VARCHAR(20),
    `telefone` VARCHAR(20),
    `endereco` VARCHAR(255),
    `data_cadastro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB;
CREATE TABLE `password_resets` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) UNSIGNED NOT NULL,
    `token` VARCHAR(64) NOT NULL,
    `expira_em` DATETIME NOT NULL,
    `criado_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_token` (`token`),
    CONSTRAINT `fk_user_reset` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;
CREATE TABLE `produtos` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `nome` VARCHAR(255) NOT NULL,
    `descricao` TEXT,
    `preco` DECIMAL(10, 2) NOT NULL,
    `imagem_url` VARCHAR(255),
    `categoria` VARCHAR(50) NOT NULL,
    `avaliacao_media` DECIMAL(2, 1) NOT NULL DEFAULT 0.0,
    `is_promocao` BOOLEAN NOT NULL DEFAULT FALSE,
    `is_novidade` BOOLEAN NOT NULL DEFAULT FALSE,
    `preco_promocional` DECIMAL(10, 2) NULL
) ENGINE = InnoDB;
-- 4. Criação da tabela 'produto_imagens'
CREATE TABLE `produto_imagens` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `produto_id` INT NOT NULL,
    `imagem_url` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`produto_id`) REFERENCES `produtos`(`id`) ON DELETE CASCADE
) ENGINE = InnoDB;
CREATE TABLE produto_variacoes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `produto_id` INT NOT NULL,
    `tamanho` VARCHAR(20) NOT NULL,
    `estoque` INT NOT NULL DEFAULT 0,
    FOREIGN KEY (`produto_id`) REFERENCES produtos(`id`) ON DELETE CASCADE,
    UNIQUE KEY idx_variacao_unica_simples (produto_id, tamanho)
) ENGINE = InnoDB;
CREATE TABLE carrinho_itens (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT(11) UNSIGNED NULL,
    `carrinho_session_id` VARCHAR(255) NULL,
    `produto_id` INT NOT NULL,
    `variacao_id` INT NOT NULL,
    `quantidade` INT NOT NULL DEFAULT 1,
    `data_adicao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usuario_id`) REFERENCES usuarios(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`produto_id`) REFERENCES produtos(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`variacao_id`) REFERENCES produto_variacoes(`id`) ON DELETE CASCADE,
    UNIQUE KEY uk_user_variacao (usuario_id, variacao_id),
    UNIQUE KEY uk_session_variacao (carrinho_session_id, variacao_id)
) ENGINE = InnoDB;
-- 7. Tabela de Pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) UNSIGNED NOT NULL,
    data_pedido DATETIME DEFAULT CURRENT_TIMESTAMP,
    total_produtos DECIMAL(10, 2) NOT NULL,
    valor_frete DECIMAL(10, 2) NOT NULL,
    valor_desconto DECIMAL(10, 2) NOT NULL,
    total_geral DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'Pendente',
    metodo_pagamento VARCHAR(50) NOT NULL,
    endereco_completo TEXT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE = InnoDB;
CREATE TABLE itens_pedido (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT,
    nome_produto VARCHAR(255) NOT NULL,
    tamanho VARCHAR(10),
    quantidade INT NOT NULL,
    preco_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE
    SET NULL
) ENGINE = InnoDB;
-- 9. Tabela de Avaliações (Item 25)
CREATE TABLE produto_avaliacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    usuario_id INT(11) UNSIGNED NOT NULL,
    nota INT NOT NULL,
    titulo VARCHAR(100),
    comentario TEXT,
    data_avaliacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_produto (usuario_id, produto_id)
) ENGINE = InnoDB;
-- 2.10 Tabela 'carrossel_slides'
CREATE TABLE carrossel_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagem_url VARCHAR(255) NOT NULL,
    link_url VARCHAR(255) NULL,
    titulo VARCHAR(100) NULL,
    subtitulo VARCHAR(200) NULL,
    ordem INT DEFAULT 0
) ENGINE = InnoDB;
-- 2.11 Tabela 'contato_mensagens'
CREATE TABLE IF NOT EXISTS contato_mensagens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NULL,
    mensagem TEXT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('nao_lido', 'lido') NOT NULL DEFAULT 'nao_lido'
) ENGINE = InnoDB;
-- 2.12 Tabela 'configuracoes'
CREATE TABLE configuracoes (
    config_key VARCHAR(50) PRIMARY KEY,
    config_value TEXT NULL
) ENGINE = InnoDB;
-- 2.13 Tabela 'favoritos'
-- Atualizado: usuario_id agora é INT(11) UNSIGNED
CREATE TABLE IF NOT EXISTS favoritos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT(11) UNSIGNED NOT NULL,
    produto_id INT NOT NULL,
    data_adicao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    UNIQUE KEY uk_usuario_produto (usuario_id, produto_id)
) ENGINE = InnoDB;
-- ############################################################
-- INSERÇÃO DE DADOS INICIAIS
-- ############################################################
-- Usuário Admin
INSERT INTO `usuarios` (
        id,
        nome,
        email,
        senha,
        nivel_acesso,
        cpf,
        telefone
    )
VALUES (
        1,
        'Admin Teste',
        'admin@streetstyle.com',
        '$2y$10$uGWwqhXYoBFTty1dYsPd1uszMj3uO2BVDbNNtUGqXQT/FEXbUHgIm',
        'admin',
        '12345678900',
        '11999999999'
    );
-- Usuários Clientes Fictícios (para avaliações)
INSERT IGNORE INTO usuarios (id, nome, email, senha, nivel_acesso)
VALUES (
        10,
        'Rafael Silva',
        'rafael.silva@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        11,
        'Carolina Souza',
        'carol.souza@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        12,
        'Lucas Ferreira',
        'lucas.ferreira@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        13,
        'Mariana Costa',
        'mariana.costa@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        14,
        'André Ribeiro',
        'andre.ribeiro@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        15,
        'Juliana Mendes',
        'juliana.mendes@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        16,
        'Pedro Lima',
        'pedro.lima@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        17,
        'Beatriz Rocha',
        'beatriz.rocha@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        18,
        'Felipe Gomes',
        'felipe.gomes@fake.com',
        'NaoEImportante',
        'cliente'
    ),
    (
        19,
        'Renata Martins',
        'renata.martins@fake.com',
        'NaoEImportante',
        'cliente'
    );
-- Inserção de Produtos (COM is_novidade e is_promocao definidos)
-- (Os caminhos das imagens foram limpos, removendo 'public/_ADM/')
INSERT INTO `produtos` (
        id,
        nome,
        descricao,
        preco,
        imagem_url,
        categoria,
        is_novidade,
        is_promocao,
        preco_promocional
    )
VALUES (
        1,
        'Calça Jogger 3M',
        'Modelagem jogger clássica com ajuste perfeito no corpo, confeccionada em sarja resistente de toque suave. Possui cós com elástico reforçado, cordão regulável e punhos ajustados, garantindo conforto e estilo urbano moderno.',
        179.90,
        'img/roupas/calcas/calca-jogger-3m/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        2,
        'Calça Jeans Azul',
        'Jeans azul tradicional com lavagem equilibrada e modelagem reta que valoriza o visual. Produzida com denim resistente, oferece conforto, mobilidade e versatilidade para diferentes estilos do dia a dia.',
        189.90,
        'img/roupas/calcas/calca-jeans-azul/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        3,
        'Calça Jeans Escura',
        'Jeans escuro premium com modelagem reta e tecido encorpado. Possui lavagem moderna e acabamento durável, garantindo visual elegante e caimento impecável em qualquer ocasião.',
        199.90,
        'img/roupas/calcas/calca-jeans-escura/1.webp',
        'Calças',
        0,
        0,
        169.90
    ),
    (
        4,
        'Calça Suf 4-40 Vermelha',
        'Calça em nylon leve e resistente com recortes modernos que trazem estética esportiva. O tecido de secagem rápida e o ajuste confortável tornam a peça ideal para looks urbanos.',
        159.90,
        'img/roupas/calcas/calca-suf-4-40-vermelha/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        5,
        'Calça Patch Nylon',
        'Calça em nylon reforçado com modelagem reta e recortes estilizados. Leve, confortável e resistente, combina com roupas oversized e traz um visual street futurista.',
        169.90,
        'img/roupas/calcas/calca-patch-nylon/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        6,
        'Calça Líquen',
        'Confeccionada em tecido brim grosso e robusto, oferece excelente durabilidade e conforto. Sua modelagem reta garante caimento perfeito e estilo urbano atemporal.',
        209.90,
        'img/roupas/calcas/calca-liquen/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        7,
        'Calça Caqui Escuro',
        'Calça de sarja caqui com modelagem padrão e tecido resistente. Versátil e confortável, ideal para o dia a dia.',
        179.90,
        'img/roupas/calcas/calca-caqui-escuro/1.webp',
        'Calças',
        0,
        1,
        149.90
    ),
    (
        8,
        'Calça Estonada',
        'Calça em denim preto estonado com acabamento macio e visual vintage moderno. Sua modelagem valoriza o corpo e entrega estilo marcante para composições urbanas.',
        219.90,
        'img/roupas/calcas/calca-estonada/1.webp',
        'Calças',
        0,
        0,
        NULL
    ),
    (
        34,
        'Calça Dyer Stoned Preta',
        'Calça reta com lavagem stone e acabamento premium. Produzida em denim encorpado, oferece durabilidade e estética street autêntica.',
        209.90,
        'img/roupas/calcas/calca-dyer-stoned-preta/1.webp',
        'Calças',
        1,
        0,
        NULL
    ),
    (
        9,
        'Camiseta Lizard Amarela',
        'Camiseta de modelagem média, confeccionada em algodão confortável e estampa vibrante inspirada no streetwear. Leve e respirável, perfeita para o uso diário.',
        89.90,
        'img/roupas/camisetas/camiseta-lizard-amarela/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        10,
        'Camiseta Albatroz',
        'Camiseta em algodão premium com caimento estruturado e estampa exclusiva. Ideal para quem busca estilo urbano com personalidade.',
        109.90,
        'img/roupas/camisetas/camiseta-albatroz/1.webp',
        'Camisetas',
        0,
        1,
        89.90
    ),
    (
        11,
        'Camiseta Pipa Verde',
        'Camiseta leve e confortável com estampa minimalista. Sua modelagem equilibrada garante ótimo caimento em qualquer combinação.',
        89.90,
        'img/roupas/camisetas/camiseta-pipa-verde/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        12,
        'Manga Longa Strength Branca',
        'Camiseta manga longa com tecido encorpado e corte moderno. Ideal para dias frescos, proporcionando estilo e conforto sem abrir mão da estética clean.',
        109.90,
        'img/roupas/camisetas/manga-longa-strength-branca/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        13,
        'Camiseta World Of Mine Preta',
        'Camiseta com modelagem boxy e estampa conceitual que remete ao underground moderno. Tecido robusto e visual marcante.',
        99.90,
        'img/roupas/camisetas/camiseta-world-of-mine-preta/1.webp',
        'Camisetas',
        0,
        1,
        79.90
    ),
    (
        14,
        'Camiseta Name Logo Cinza',
        'Camiseta de malha espessa com logo minimalista e caimento estruturado. Ideal para quem gosta de estilo clean com toque premium.',
        119.90,
        'img/roupas/camisetas/camiseta-name-logo-cinza/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        15,
        'Camiseta X-High Branca',
        'Camiseta leve com tecido macio e estampa moderna. Possui modelagem média que se adapta ao corpo com conforto.',
        89.90,
        'img/roupas/camisetas/camiseta-x-high-branca/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        16,
        'Camiseta Freak Bege',
        'Camiseta estonada com efeito vintage, modelagem confortável e estampa exclusiva que destaca autenticidade e estilo.',
        99.90,
        'img/roupas/camisetas/camiseta-freak-bege/1.webp',
        'Camisetas',
        0,
        0,
        NULL
    ),
    (
        35,
        'Camiseta Arqueologs Branca',
        'Camiseta boxy confeccionada em tecido grosso de alta gramatura. A estampa artística traz estética conceitual e moderna.',
        179.90,
        'img/roupas/camisetas/camiseta-arqueologos-branca/1.webp',
        'Camisetas',
        1,
        0,
        NULL
    ),
    (
        17,
        'Conjunto Verão 2025',
        'Conjunto leve e respirável, ideal para dias quentes. Tecido confortável e macio, design moderno e visual versátil para looks completos sem esforço.',
        249.90,
        'img/roupas/conjuntos/1.jpg',
        'Conjuntos',
        1,
        0,
        NULL
    ),
    (
        18,
        'Suéter X-Ray',
        'Suéter grosso com alta gramatura, toque macio e ótimo isolamento térmico. A estampa X-Ray adiciona personalidade ao look.',
        229.90,
        'img/roupas/moletons/sueter-x-ray/1.webp',
        'Moletons',
        1,
        0,
        NULL
    ),
    (
        19,
        'Casaco Funghi',
        'Casaco de tricô pesado com textura marcante e caimento aconchegante. Perfeito para dias frios, entrega estilo e conforto.',
        249.90,
        'img/roupas/moletons/casaco-funghi/1.webp',
        'Moletons',
        0,
        0,
        NULL
    ),
    (
        20,
        'Moletom Barra Textura Branco',
        'Moletom encorpado com detalhe texturizado na barra e toque suave. Visual clean e moderno para qualquer estilo.',
        259.90,
        'img/roupas/moletons/moletom-barra-textura-branco/1.webp',
        'Moletons',
        0,
        0,
        NULL
    ),
    (
        21,
        'Moletom Boêmios Branco',
        'Moletom com tricô espesso, estampa exclusiva e acabamento robusto. Combina conforto térmico com estética minimalista.',
        259.90,
        'img/roupas/moletons/moletom-boemios-branco/1.webp',
        'Moletons',
        0,
        0,
        NULL
    ),
    (
        22,
        'Suéter Zushi',
        'Suéter com modelagem ampla e tecido macio. Inspirado na estética japonesa moderna, oferece estilo e conforto premium.',
        239.90,
        'img/roupas/moletons/sueter-zushi/1.webp',
        'Moletons',
        0,
        1,
        199.90
    ),
    (
        23,
        'Moletom Falador Passa Mal',
        'Moletom de textura pesada, toque confortável e estampa irreverente. Perfeito para looks despojados.',
        259.90,
        'img/roupas/moletons/moletom-falador-passa-mal/1.webp',
        'Moletons',
        0,
        0,
        NULL
    ),
    (
        24,
        'Casaco Trippy',
        'Casaco fleece estampado com textura macia e visual psicodélico. Aquece bem e garante estilo diferenciado.',
        299.90,
        'img/roupas/moletons/casaco-trippy/1.webp',
        'Moletons',
        0,
        1,
        249.90
    ),
    (
        25,
        'Moletom Goods Logo Preto',
        'Moletom de fleece grosso com estampa minimalista e acabamento premium. Ideal para looks urbanos modernos.',
        289.90,
        'img/roupas/moletons/moletom-goods-logo-preto/1.webp',
        'Moletons',
        0,
        0,
        NULL
    ),
    (
        36,
        'Tricô Mountain',
        'Moletom em tricô de alta qualidade, padrão “Mountain” e acabamento robusto. Peça premium ideal para dias frios.',
        589.00,
        'img/roupas/moletons/trico-mountain/1.webp',
        'Moletons',
        1,
        0,
        NULL
    ),
    (
        26,
        'Shorts Layered Laranja',
        'Shorts leve de camada dupla com tecido respirável e secagem rápida. Ideal para treinos e uso casual, garantindo conforto e liberdade de movimento.',
        129.90,
        'img/roupas/shorts/shorts-layered-laranja/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        27,
        'Shorts Kierk Jeans Preto',
        'Shorts jeans com lavagem escura e tecido confortável. Combina mobilidade com estilo street, ideal para dias quentes.',
        139.90,
        'img/roupas/shorts/shorts-kierk-jeans-preto/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        28,
        'Short Hider Preto',
        'Short em tactel leve com secagem rápida e modelagem confortável. Perfeito para treinos, lazer e composições casuais.',
        119.90,
        'img/roupas/shorts/short-hider-preto/1.webp',
        'Shorts',
        0,
        1,
        99.90
    ),
    (
        29,
        'Bermuda Jeans',
        'Bermuda jeans encorpada com visual clássico e tecido robusto. Durável, confortável e perfeita para o dia a dia.',
        159.90,
        'img/roupas/shorts/bermuda-jeans/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        30,
        'Shorts Goods Logo',
        'Shorts com tecido grosso e confortável, acabamento premium e logo minimalista. Ideal para looks urbanos.',
        149.90,
        'img/roupas/shorts/shorts-goods-logo/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        31,
        'Bermuda Jeans Coração B',
        'Bermuda jeans robusta com detalhes em forma de coração, trazendo estilo personalizado e visual único.',
        159.90,
        'img/roupas/shorts/bermuda-jeans-coracao-b/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        32,
        'Shorts X-High Preto',
        'Shorts de tecido grosso com modelagem confortável e estética minimalista. Versátil para diversos looks.',
        149.90,
        'img/roupas/shorts/shorts-x-high-preto/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        33,
        'Shorts Kierk Verde',
        'Shorts leve, resistente e confortável, com design moderno e ideal para atividades casuais.',
        139.90,
        'img/roupas/shorts/shorts-kierk-verde/1.webp',
        'Shorts',
        0,
        0,
        NULL
    ),
    (
        37,
        'Bermuda Tripla Preta',
        'Bermuda de modelagem ampla com visual futurista e tecido resistente. Peça confortável com estética urbana marcante.',
        169.90,
        'img/roupas/shorts/bermuda-tripla-preta/1.webp',
        'Shorts',
        1,
        0,
        NULL
    );
-- Inserção de Imagens Extras (Caminhos Limpos)
INSERT INTO `produto_imagens` (produto_id, imagem_url)
VALUES (1, 'img/roupas/calcas/calca-jogger-3m/2.webp'),
    (1, 'img/roupas/calcas/calca-jogger-3m/3.webp'),
    (1, 'img/roupas/calcas/calca-jogger-3m/4.webp'),
    (2, 'img/roupas/calcas/calca-jeans-azul/2.webp'),
    (2, 'img/roupas/calcas/calca-jeans-azul/3.webp'),
    (3, 'img/roupas/calcas/calca-jeans-escura/2.webp'),
    (3, 'img/roupas/calcas/calca-jeans-escura/3.webp'),
    (
        4,
        'img/roupas/calcas/calca-suf-4-40-vermelha/2.webp'
    ),
    (
        4,
        'img/roupas/calcas/calca-suf-4-40-vermelha/3.webp'
    ),
    (5, 'img/roupas/calcas/calca-patch-nylon/2.webp'),
    (5, 'img/roupas/calcas/calca-patch-nylon/3.webp'),
    (5, 'img/roupas/calcas/calca-patch-nylon/4.webp'),
    (6, 'img/roupas/calcas/calca-liquen/2.webp'),
    (6, 'img/roupas/calcas/calca-liquen/3.webp'),
    (8, 'img/roupas/calcas/calca-estonada/2.webp'),
    (8, 'img/roupas/calcas/calca-estonada/3.webp'),
    (8, 'img/roupas/calcas/calca-estonada/4.webp'),
    (
        34,
        'img/roupas/calcas/calca-dyer-stoned-preta/2.webp'
    ),
    (
        9,
        'img/roupas/camisetas/camiseta-lizard-amarela/2.webp'
    ),
    (
        10,
        'img/roupas/camisetas/camiseta-albatroz/2.webp'
    ),
    (
        11,
        'img/roupas/camisetas/camiseta-pipa-verde/2.webp'
    ),
    (
        12,
        'img/roupas/camisetas/manga-longa-strength-branca/2.webp'
    ),
    (
        13,
        'img/roupas/camisetas/camiseta-world-of-mine-preta/2.webp'
    ),
    (
        13,
        'img/roupas/camisetas/camiseta-world-of-mine-preta/3.webp'
    ),
    (
        14,
        'img/roupas/camisetas/camiseta-name-logo-cinza/2.webp'
    ),
    (
        15,
        'img/roupas/camisetas/camiseta-x-high-branca/2.webp'
    ),
    (
        15,
        'img/roupas/camisetas/camiseta-x-high-branca/3.webp'
    ),
    (
        35,
        'img/roupas/camisetas/camiseta-arqueologos-branca/2.webp'
    ),
    (17, 'img/roupas/conjuntos/2.jpg'),
    (17, 'img/roupas/conjuntos/3.jpg'),
    (17, 'img/roupas/conjuntos/4.jpg'),
    (18, 'img/roupas/moletons/sueter-x-ray/2.webp'),
    (19, 'img/roupas/moletons/casaco-funghi/2.webp'),
    (19, 'img/roupas/moletons/casaco-funghi/3.webp'),
    (19, 'img/roupas/moletons/casaco-funghi/4.webp'),
    (
        20,
        'img/roupas/moletons/moletom-barra-textura-branco/2.webp'
    ),
    (
        21,
        'img/roupas/moletons/moletom-boemios-branco/2.webp'
    ),
    (22, 'img/roupas/moletons/sueter-zushi/2.webp'),
    (
        23,
        'img/roupas/moletons/moletom-falador-passa-mal/2.webp'
    ),
    (24, 'img/roupas/moletons/casaco-trippy/2.webp'),
    (36, 'img/roupas/moletons/trico-mountain/2.webp'),
    (36, 'img/roupas/moletons/trico-mountain/3.webp'),
    (
        26,
        'img/roupas/shorts/shorts-layered-laranja/2.webp'
    ),
    (
        26,
        'img/roupas/shorts/shorts-layered-laranja/3.webp'
    ),
    (
        26,
        'img/roupas/shorts/shorts-layered-laranja/4.webp'
    ),
    (
        27,
        'img/roupas/shorts/shorts-kierk-jeans-preto/2.webp'
    ),
    (28, 'img/roupas/shorts/short-hider-preto/2.webp'),
    (28, 'img/roupas/shorts/short-hider-preto/3.webp'),
    (29, 'img/roupas/shorts/bermuda-jeans/2.webp'),
    (29, 'img/roupas/shorts/bermuda-jeans/3.webp'),
    (30, 'img/roupas/shorts/shorts-goods-logo/2.webp'),
    (30, 'img/roupas/shorts/shorts-goods-logo/3.webp'),
    (
        31,
        'img/roupas/shorts/bermuda-jeans-coracao-b/2.webp'
    ),
    (
        31,
        'img/roupas/shorts/bermuda-jeans-coracao-b/3.webp'
    ),
    (
        31,
        'img/roupas/shorts/bermuda-jeans-coracao-b/4.webp'
    ),
    (
        32,
        'img/roupas/shorts/shorts-x-high-preto/2.webp'
    ),
    (
        32,
        'img/roupas/shorts/shorts-x-high-preto/3.webp'
    ),
    (
        32,
        'img/roupas/shorts/shorts-x-high-preto/4.webp'
    ),
    (
        33,
        'img/roupas/shorts/shorts-kierk-verde/2.webp'
    ),
    (
        33,
        'img/roupas/shorts/shorts-kierk-verde/3.webp'
    ),
    (
        37,
        'img/roupas/shorts/bermuda-tripla-preta/2.webp'
    ),
    (
        37,
        'img/roupas/shorts/bermuda-tripla-preta/3.webp'
    );
-- Inserção de Variações
INSERT INTO `produto_variacoes` (produto_id, tamanho, estoque)
VALUES (1, 'P', 10),
    (1, 'M', 15),
    (1, 'G', 8),
    (1, 'GG', 5),
    (2, 'P', 5),
    (2, 'M', 10),
    (2, 'G', 15),
    (2, 'GG', 7),
    (3, 'P', 12),
    (3, 'M', 10),
    (3, 'G', 9),
    (3, 'GG', 3),
    (4, 'P', 8),
    (4, 'M', 11),
    (4, 'G', 6),
    (4, 'GG', 4),
    (5, 'P', 7),
    (5, 'M', 13),
    (5, 'G', 10),
    (5, 'GG', 6),
    (6, 'P', 10),
    (6, 'M', 15),
    (6, 'G', 12),
    (6, 'GG', 5),
    (7, 'P', 9),
    (7, 'M', 14),
    (7, 'G', 11),
    (7, 'GG', 7),
    (8, 'P', 6),
    (8, 'M', 10),
    (8, 'G', 9),
    (8, 'GG', 3),
    (34, 'P', 11),
    (34, 'M', 16),
    (34, 'G', 8),
    (34, 'GG', 4),
    (9, 'P', 5),
    (9, 'M', 10),
    (9, 'G', 15),
    (9, 'GG', 8),
    (10, 'P', 10),
    (10, 'M', 14),
    (10, 'G', 11),
    (10, 'GG', 5),
    (11, 'P', 7),
    (11, 'M', 12),
    (11, 'G', 10),
    (11, 'GG', 6),
    (12, 'P', 10),
    (12, 'M', 15),
    (12, 'G', 10),
    (12, 'GG', 7),
    (13, 'P', 8),
    (13, 'M', 13),
    (13, 'G', 9),
    (13, 'GG', 4),
    (14, 'P', 6),
    (14, 'M', 11),
    (14, 'G', 12),
    (14, 'GG', 5),
    (15, 'P', 10),
    (15, 'M', 15),
    (15, 'G', 13),
    (15, 'GG', 8),
    (16, 'P', 9),
    (16, 'M', 14),
    (16, 'G', 10),
    (16, 'GG', 6),
    (35, 'P', 12),
    (35, 'M', 16),
    (35, 'G', 11),
    (35, 'GG', 7),
    (17, 'P', 5),
    (17, 'M', 9),
    (17, 'G', 6),
    (17, 'GG', 3),
    (18, 'P', 10),
    (18, 'M', 15),
    (18, 'G', 12),
    (18, 'GG', 7),
    (19, 'P', 6),
    (19, 'M', 10),
    (19, 'G', 8),
    (19, 'GG', 5),
    (20, 'P', 7),
    (20, 'M', 11),
    (20, 'G', 9),
    (20, 'GG', 6),
    (21, 'P', 7),
    (21, 'M', 12),
    (21, 'G', 10),
    (21, 'GG', 4),
    (22, 'P', 8),
    (22, 'M', 13),
    (22, 'G', 11),
    (22, 'GG', 5),
    (23, 'P', 9),
    (23, 'M', 14),
    (23, 'G', 10),
    (23, 'GG', 6),
    (24, 'P', 5),
    (24, 'M', 9),
    (24, 'G', 7),
    (24, 'GG', 4),
    (25, 'P', 6),
    (25, 'M', 10),
    (25, 'G', 8),
    (25, 'GG', 5),
    (36, 'P', 4),
    (36, 'M', 7),
    (36, 'G', 5),
    (36, 'GG', 3),
    (26, 'P', 10),
    (26, 'M', 15),
    (26, 'G', 12),
    (26, 'GG', 8),
    (27, 'P', 8),
    (27, 'M', 13),
    (27, 'G', 10),
    (27, 'GG', 6),
    (28, 'P', 9),
    (28, 'M', 14),
    (28, 'G', 11),
    (28, 'GG', 7),
    (29, 'P', 7),
    (29, 'M', 12),
    (29, 'G', 9),
    (29, 'GG', 5),
    (30, 'P', 11),
    (30, 'M', 16),
    (30, 'G', 13),
    (30, 'GG', 9),
    (31, 'P', 6),
    (31, 'M', 10),
    (31, 'G', 8),
    (31, 'GG', 4),
    (32, 'P', 10),
    (32, 'M', 15),
    (32, 'G', 12),
    (32, 'GG', 7),
    (33, 'P', 9),
    (33, 'M', 14),
    (33, 'G', 11),
    (33, 'GG', 6),
    (37, 'P', 7),
    (37, 'M', 12),
    (37, 'G', 9),
    (37, 'GG', 5);
-- Inserção de Avaliações Fictícias
INSERT INTO produto_avaliacoes (produto_id, usuario_id, nota, titulo, comentario)
VALUES -- (Notas 2-5, Comentários consistentes)
    (
        1,
        10,
        5,
        'Incrível!',
        'Caimento impecável e o material refletivo é muito estiloso à noite.'
    ),
    (
        1,
        11,
        4,
        'Recomendo',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        1,
        12,
        4,
        'Bom custo-benefício',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        1,
        13,
        5,
        'Material excelente',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        1,
        14,
        4,
        'Muito bom',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        1,
        15,
        3,
        'Poderia ser melhor',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        1,
        16,
        5,
        'Perfeito!',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        1,
        17,
        5,
        'Qualidade surpreendente',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        1,
        18,
        2,
        'Não gostei',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        1,
        19,
        5,
        'Amei!',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        2,
        10,
        5,
        'Excelente!',
        'Veste muito bem, o tom de azul é lindo e a cor é bem viva.'
    ),
    (
        2,
        11,
        4,
        'Recomendo',
        'Confortável e de boa qualidade. Entrega rápida.'
    ),
    (
        2,
        12,
        5,
        'Perfeita',
        'A modelagem é moderna, ficou soltinha sem ser larga. Ótima compra.'
    ),
    (
        2,
        13,
        3,
        'Poderia ser melhor',
        'O botão parece um pouco frágil. De resto, tudo certo.'
    ),
    (
        2,
        14,
        5,
        'Ótima Compra',
        'Caimento perfeito! Veste muito bem, o jeans é macio.'
    ),
    (
        2,
        15,
        4,
        'Bom',
        'Achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        2,
        16,
        5,
        'Amei!',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem.'
    ),
    (
        2,
        17,
        4,
        'Surpreendente',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (2, 18, 5, 'Super Indico', 'Material excelente.'),
    (
        2,
        19,
        3,
        'Não serviu',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        2,
        1,
        2,
        'Péssimo',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        3,
        10,
        5,
        'Caimento impecável',
        'O melhor jeans que já comprei. O tecido pesado realmente dá um caimento diferenciado.'
    ),
    (
        3,
        11,
        5,
        'Maravilhosa',
        'Ficou perfeita, a cintura ajustável é um diferencial e tanto.'
    ),
    (
        3,
        12,
        4,
        'Bom',
        'A cor é fiel à foto, mas achei um pouco pesada para dias quentes.'
    ),
    (
        3,
        13,
        5,
        'Show!',
        'Chegou super rápido e em perfeitas condições.'
    ),
    (
        3,
        14,
        3,
        'Não gostei',
        'O tamanho G ficou pequeno para mim, tive que pedir a troca.'
    ),
    (
        3,
        15,
        5,
        'Recomendo!',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        3,
        16,
        4,
        'Estilosa',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        3,
        17,
        5,
        'Valeu o preço',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        3,
        18,
        4,
        'Confortável',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        3,
        19,
        5,
        'Top',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        3,
        1,
        4,
        'Gostei',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        4,
        10,
        4,
        'Visual Urbano',
        'Tecido leve e resistente. Ótimo para quem busca um estilo mais tático.'
    ),
    (
        4,
        12,
        5,
        'Ótimo',
        'Ficou um pouco comprida, mas o ajuste no tornozelo resolveu.'
    ),
    (
        4,
        15,
        4,
        'Recomendo',
        'Pode comprar sem medo, a calça é muito boa.'
    ),
    (
        4,
        18,
        5,
        'Show!',
        'Produto de altíssima qualidade.'
    ),
    (4, 11, 4, 'Aprovada', 'Bom caimento.'),
    (
        4,
        13,
        3,
        'Esperava mais',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (
        4,
        14,
        2,
        'Fraca',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (4, 16, 5, 'Nota 5', 'Confortável e resistente.'),
    (4, 17, 4, 'Aprovada', 'Gostei da cor.'),
    (4, 19, 5, 'Excelente!', 'Muito estiloso.'),
    (
        5,
        15,
        4,
        'Boa qualidade',
        'O patch em velcro é divertido. Calça bonita e bem feita.'
    ),
    (
        5,
        18,
        5,
        'Excelente',
        'Superou as expectativas. É o que eu procurava.'
    ),
    (
        5,
        11,
        4,
        'Top',
        'Veste bem, chegou muito rápido.'
    ),
    (
        5,
        10,
        5,
        'Show!',
        'Produto de altíssima qualidade.'
    ),
    (5, 12, 4, 'Aprovada', 'Bom caimento.'),
    (
        5,
        13,
        5,
        'Excelente',
        'Perfeito para o dia a dia.'
    ),
    (5, 14, 4, 'Valeu', 'Chegou rápido.'),
    (
        6,
        11,
        4,
        'Quentinha',
        'Moletom macio e a cor é linda, bem discreta.'
    ),
    (
        6,
        13,
        5,
        'Veste bem',
        'Caimento moderno. Gostei.'
    ),
    (6, 18, 4, 'Show!', 'Produto muito bem feito.'),
    (
        6,
        14,
        3,
        'Poderia ser melhor',
        'O botão parece um pouco frágil. De resto, tudo certo.'
    ),
    (
        6,
        16,
        5,
        'Ótima Compra',
        'Caimento perfeito! Veste muito bem, o jeans é macio.'
    ),
    (
        6,
        17,
        4,
        'Bom',
        'Achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        7,
        14,
        5,
        'Excelente!',
        'Veste muito bem, o tom de caqui é perfeito.'
    ),
    (7, 17, 5, 'Amei!', 'Ficou na medida certa.'),
    (
        7,
        19,
        4,
        'Bom',
        'Achei o tecido um pouco áspero, mas é bonito.'
    ),
    (
        7,
        10,
        5,
        'Perfeita',
        'Não tem como errar! Camiseta leve e que dura bastante.'
    ),
    (
        7,
        11,
        4,
        'Bom custo-benefício',
        'Pelo preço, a qualidade é excelente. Gostei da gola.'
    ),
    (
        8,
        10,
        5,
        'Super Confortável',
        'Caimento perfeito e o tecido é robusto.'
    ),
    (
        8,
        12,
        4,
        'Gostei',
        'Demorou um pouco para chegar, mas valeu a espera.'
    ),
    (
        8,
        13,
        3,
        'Fraca',
        'O tecido é muito fino. Não recomendo.'
    ),
    (
        8,
        14,
        5,
        'Nota 10',
        'Camisa de altíssima qualidade, vale o investimento.'
    ),
    (
        8,
        16,
        4,
        'Recomendo',
        'Pode comprar sem medo, a camiseta é muito boa.'
    ),
    (
        9,
        14,
        5,
        'Cor Perfeita',
        'A cor é muito mais vibrante ao vivo. Adorei!'
    ),
    (
        9,
        17,
        4,
        'Casual',
        'Camiseta simples mas de boa qualidade.'
    ),
    (9, 11, 5, 'Top D+', 'Produto de alta qualidade.'),
    (
        9,
        12,
        3,
        'Poderia ser melhor',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        9,
        15,
        5,
        'Qualidade surpreendente',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        10,
        15,
        4,
        'Básica e Confortável',
        'Ótimo tecido para o verão, não esquenta. O logo é discreto.'
    ),
    (
        10,
        18,
        5,
        'Perfeita',
        'Não tem como errar! Camiseta leve e que dura bastante.'
    ),
    (
        10,
        19,
        3,
        'Muito bom',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        10,
        13,
        4,
        'Poderia ser melhor',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        10,
        11,
        5,
        'Cinco Estrelas',
        'Material excelente.'
    ),
    (
        11,
        12,
        5,
        'Show!',
        'Veste bem, chegou muito rápido, vou comprar outras cores.'
    ),
    (
        11,
        13,
        3,
        'Fraca',
        'O tecido é muito fino. Não recomendo.'
    ),
    (
        11,
        16,
        5,
        'Nota 10',
        'Camisa de altíssima qualidade, vale o investimento.'
    ),
    (
        11,
        1,
        4,
        'Gostei',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        11,
        19,
        5,
        'Qualidade surpreendente',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        12,
        17,
        4,
        'Recomendo',
        'Pode comprar sem medo, a camiseta é muito boa.'
    ),
    (12, 19, 5, 'Top', 'Excelente material.'),
    (
        12,
        18,
        3,
        'Não era o que eu esperava',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        12,
        16,
        4,
        'Material excelente',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        12,
        11,
        2,
        'Recomendo',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        12,
        15,
        5,
        'Poderia ser melhor',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (13, 10, 5, 'Maravilhosa', 'Adorei a estampa.'),
    (13, 12, 4, 'Ótima', 'Veste super bem.'),
    (13, 15, 5, 'Perfeita', 'Caimento impecável.'),
    (
        13,
        14,
        3,
        'Bom custo-benefício',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        13,
        16,
        5,
        'Qualidade surpreendente',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        14,
        11,
        2,
        'Gostei',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        14,
        10,
        4,
        'Poderia ser melhor',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        14,
        18,
        5,
        'Material excelente',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        14,
        19,
        4,
        'Bom custo-benefício',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (
        14,
        12,
        3,
        'Qualidade surpreendente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        15,
        13,
        5,
        'Excelente',
        'Camiseta de alta qualidade.'
    ),
    (
        15,
        14,
        4,
        'Boa',
        'O tecido é muito confortável.'
    ),
    (
        15,
        11,
        3,
        'Péssimo',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        15,
        1,
        3,
        'Material excelente',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        15,
        12,
        4,
        'Material excelente',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        15,
        15,
        4,
        'Poderia ser melhor',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (16, 17, 5, 'Amei', 'A cor bege é linda.'),
    (16, 19, 4, 'Recomendo', 'Pode comprar sem medo.'),
    (
        16,
        18,
        5,
        'Não era o que eu esperava',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        16,
        14,
        5,
        'Bom custo-benefício',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (
        16,
        12,
        5,
        'Não era o que eu esperava',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        16,
        13,
        4,
        'Não era o que eu esperava',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        16,
        10,
        5,
        'Incrível!',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        17,
        10,
        4,
        'Conjunto Verão',
        'O tecido é leve, mas o shorts poderia ser um pouco mais folgado.'
    ),
    (
        17,
        12,
        5,
        'Perfeito',
        'Ótima combinação de cores e tecido. Ideal para o calor.'
    ),
    (
        17,
        16,
        5,
        'Gostei',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        17,
        14,
        2,
        'Não gostei da cor',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        17,
        11,
        3,
        'Muito bom',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        18,
        16,
        5,
        'Melhor Compra',
        'O tamanho M ficou perfeito, o caimento é top e é confortável.'
    ),
    (
        18,
        19,
        4,
        'Amei!',
        'Chegou no prazo e o material é macio.'
    ),
    (
        18,
        13,
        5,
        'Excelente',
        'Muito bom, vestiu super bem, as cores são discretas.'
    ),
    (
        18,
        14,
        4,
        'Qualidade',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        18,
        15,
        5,
        'Incrível!',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (19, 11, 5, 'Excelente', 'Moletom perfeito.'),
    (19, 18, 4, 'Quente', 'Muito bom para o frio.'),
    (
        19,
        10,
        4,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        19,
        13,
        3,
        'Poderia ser melhor',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        19,
        12,
        2,
        'Material excelente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (20, 15, 5, 'Veste bem', 'Ótima qualidade.'),
    (20, 12, 4, 'Recomendo', 'Adorei a estampa.'),
    (
        20,
        1,
        4,
        'Poderia ser melhor',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        20,
        11,
        4,
        'Qualidade surpreendente',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        20,
        17,
        5,
        'Incrível!',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        20,
        18,
        2,
        'Material excelente',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        21,
        17,
        4,
        'Bom custo-benefício',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        21,
        15,
        3,
        'Bom custo-benefício',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        21,
        14,
        4,
        'Muito bom',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        21,
        10,
        2,
        'Recomendo',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        21,
        19,
        4,
        'Perfeito!',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        21,
        13,
        4,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (22, 16, 5, 'Perfeito', 'O moletom é lindo.'),
    (22, 19, 4, 'Show', 'Shorts confortável.'),
    (
        22,
        13,
        4,
        'Material excelente',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        22,
        1,
        2,
        'Bom custo-benefício',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (
        22,
        10,
        3,
        'Bom custo-benefício',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        22,
        11,
        3,
        'Qualidade surpreendente',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        23,
        17,
        5,
        'Muito bom',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        23,
        13,
        3,
        'Qualidade surpreendente',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        23,
        19,
        5,
        'Recomendo',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        23,
        11,
        5,
        'Recomendo',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        23,
        18,
        5,
        'Poderia ser melhor',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (24, 12, 5, 'Perfeita', 'Adorei o caimento.'),
    (24, 15, 4, 'Boa', 'Bom custo-benefício.'),
    (
        24,
        16,
        5,
        'Material excelente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        24,
        11,
        4,
        'Qualidade surpreendente',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        24,
        13,
        5,
        'Incrível!',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        24,
        1,
        2,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        25,
        18,
        5,
        'Maravilhosa',
        'Shorts jeans de qualidade.'
    ),
    (25, 19, 4, 'Ótimo', 'Perfeito para o verão.'),
    (
        25,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        25,
        13,
        3,
        'Poderia ser melhor',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        25,
        10,
        4,
        'Incrível!',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        25,
        11,
        5,
        'Cinco Estrelas',
        'Material excelente.'
    ),
    (26, 13, 5, 'Excelente', 'Shorts muito bom.'),
    (
        26,
        14,
        4,
        'Recomendo',
        'A calça veste super bem.'
    ),
    (
        26,
        16,
        5,
        'Material excelente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        26,
        12,
        5,
        'Não era o que eu esperava',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        26,
        11,
        4,
        'Qualidade surpreendente',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        26,
        1,
        2,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (27, 17, 5, 'Top', 'Camiseta linda.'),
    (27, 10, 4, 'Quente', 'Tricô confortável.'),
    (
        27,
        19,
        5,
        'Gostei',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        27,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        27,
        18,
        5,
        'Muito bom',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (28, 12, 5, 'Perfeito', 'Shorts triplo estiloso.'),
    (28, 15, 4, 'Boa', 'Caimento impecável.'),
    (
        28,
        16,
        4,
        'Material excelente',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        28,
        13,
        5,
        'Não gostei da cor',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        28,
        11,
        3,
        'Não gostei da cor',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        29,
        18,
        5,
        'Maravilhoso',
        'Ficou perfeitamente ajustada.'
    ),
    (
        29,
        11,
        4,
        'Confortável',
        'Gostei muito da modelagem.'
    ),
    (
        29,
        19,
        5,
        'Gostei',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        29,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        29,
        10,
        4,
        'Incrível!',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (
        30,
        13,
        5,
        'Excelente',
        'O material é de primeira.'
    ),
    (30, 14, 4, 'Top', 'Recomendo a todos.'),
    (
        30,
        16,
        4,
        'Material excelente',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        30,
        12,
        4,
        'Muito bom',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        30,
        11,
        3,
        'Não gostei da cor',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (31, 17, 5, 'Perfeita', 'Ajuste ótimo!'),
    (31, 10, 4, 'Bom', 'A cor é fiel à foto.'),
    (
        31,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        31,
        18,
        5,
        'Muito bom',
        'Esperava mais. A descrição dizia ''tecido robusto'' mas achei bem fino. Não é ruim, mas não é o que foi prometido.'
    ),
    (
        31,
        13,
        3,
        'Poderia ser melhor',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (32, 12, 5, 'Show!', 'Shorts muito estiloso.'),
    (32, 15, 4, 'Excelente', 'Veste super bem.'),
    (
        32,
        16,
        4,
        'Material excelente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        32,
        11,
        4,
        'Qualidade surpreendente',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        32,
        13,
        5,
        'Incrível!',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        32,
        1,
        2,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (33, 18, 5, 'Maravilhoso', 'A cor é vibrante.'),
    (33, 11, 4, 'Confortável', 'Leve e fresco.'),
    (
        33,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        33,
        13,
        3,
        'Poderia ser melhor',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        33,
        10,
        4,
        'Incrível!',
        'Material de péssima qualidade, rasgou no primeiro uso. Não recomendo de forma alguma.'
    ),
    (34, 13, 5, 'Top', 'Cai muito bem.'),
    (34, 14, 4, 'Boa', 'Material de qualidade.'),
    (
        34,
        16,
        4,
        'Material excelente',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        34,
        12,
        4,
        'Muito bom',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        34,
        11,
        3,
        'Não gostei da cor',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (35, 17, 5, 'Perfeita', 'Camiseta linda.'),
    (35, 10, 4, 'Recomendo', 'Caimento muito bom.'),
    (
        35,
        12,
        3,
        'Não gostei',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (
        35,
        13,
        4,
        'Estilosa',
        'A estampa é muito bonita e parece ser bem resistente à lavagem. Valeu a pena.'
    ),
    (
        35,
        14,
        5,
        'Material excelente',
        'Um dos melhores moletons que já comprei. O caimento é perfeito e é muito quente.'
    ),
    (36, 12, 5, 'Excelente', 'Quentinho e estiloso.'),
    (36, 15, 4, 'Top', 'Moletom macio.'),
    (
        36,
        16,
        4,
        'Material excelente',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    ),
    (
        36,
        11,
        4,
        'Qualidade surpreendente',
        'Chegou rápido e bem embalado. A cor é um pouco diferente da foto, mas gostei mesmo assim.'
    ),
    (
        36,
        13,
        5,
        'Incrível!',
        'Bom, mas achei o preço um pouco alto pela qualidade. É confortável, pelo menos.'
    ),
    (
        36,
        1,
        2,
        'Recomendo',
        'Não serviu bem, o tamanho P é maior do que o normal. Tive que devolver.'
    ),
    (37, 18, 5, 'Perfeito', 'Veste super bem.'),
    (37, 11, 4, 'Boa', 'Amei a cor.'),
    (
        37,
        19,
        5,
        'Gostei',
        'O produto é exatamente como descrito. A qualidade do tecido é ótima e vestiu muito bem. Com certeza comprarei novamente.'
    ),
    (
        37,
        12,
        4,
        'Recomendo',
        'A cor é linda e o material é bom, mas a costura veio com um pequeno defeito na manga.'
    ),
    (
        37,
        13,
        3,
        'Poderia ser melhor',
        'Perfeito para o dia a dia. Leve e estiloso. Recomendo a todos.'
    );
-- 4. Recalcula a média de avaliação na tabela 'produtos'
UPDATE produtos p
    JOIN (
        SELECT produto_id,
            AVG(nota) AS media_nota
        FROM produto_avaliacoes
        GROUP BY produto_id
    ) a ON p.id = a.produto_id
SET p.avaliacao_media = ROUND(a.media_nota, 1);
-- 5. Zera a média de produtos que ficaram sem avaliações
UPDATE produtos p
    LEFT JOIN produto_avaliacoes a ON p.id = a.produto_id
SET p.avaliacao_media = 0.0
WHERE a.id IS NULL;
SELECT 'Sucesso! Avaliações fictícias inseridas e médias recalculadas.' AS status;
-- Camiseta Albatroz (ID 10): De 109.90 por 89.90
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 89.90
WHERE id = 10;
-- Camiseta World Of Mine Preta (ID 13): De 99.90 por 79.90
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 79.90
WHERE id = 13;
-- Suéter Zushi (ID 22): De 239.90 por 199.90
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 199.90
WHERE id = 22;
-- Casaco Trippy (ID 24): De 299.90 por 249.90
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 249.90
WHERE id = 24;
-- Short Hider Preto (ID 28): De 119.90 por 99.90
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 99.90
WHERE id = 28;
UPDATE produtos
SET is_novidade = 1
WHERE id IN (18, 17, 34, 35, 36);
-- ETAPA C: Marca 6 produtos como "Promoção" (com desconto)
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 89.90
WHERE id = 10;
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 79.90
WHERE id = 13;
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 199.90
WHERE id = 22;
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 249.90
WHERE id = 24;
UPDATE produtos
SET is_promocao = 1,
    preco_promocional = 99.90
WHERE id = 28;
-- Corrigir links no Rodapé/Configurações
UPDATE configuracoes
SET config_value = REPLACE(config_value, '/pj/', '/street-style-main/');
-- Corrigir links nas descrições dos produtos (caso tenha inserido imagens no texto)
UPDATE produtos
SET descricao = REPLACE(descricao, '/pj/', '/street-style-main/');
-- Corrigir links nas mensagens de contato
UPDATE contato_mensagens
SET mensagem = REPLACE(mensagem, '/pj/', '/street-style-main/');
INSERT INTO produtos (
        id,
        nome,
        descricao,
        preco,
        imagem_url,
        categoria,
        is_promocao,
        is_novidade,
        avaliacao_media
    )
VALUES (
        38,
        'Camiseta Pace Preta',
        'Feita em modelagem overboxy em tecido robusto. Estampas em Silk e gola de 2,5cm.',
        269.00,
        'img/roupas/camisetas/camiseta-pace-preta/1.webp',
        'Camisetas',
        0,
        1,
        0.0
    ),
    (
        39,
        'Camiseta Boxe Branca',
        'O clássico cabo de aço removível. Possui puídos (rasgos) e marcas de desgaste em toda extremidade da peça.',
        319.00,
        'img/roupas/camisetas/camiseta-boxe-branca/1.webp',
        'Camisetas',
        1,
        0,
        0.0
    ),
    (
        40,
        'Camiseta Tools Preta',
        'Com modelagem regular em tecido leve e confortável. Estampa em Silk e gola de 2,5cm.',
        179.00,
        'img/roupas/camisetas/camiseta-tools-preta/1.webp',
        'Camisetas',
        0,
        0,
        0.0
    );
-- 2. NOVAS CALÇAS (IDs 41, 42, 43)
INSERT INTO produtos (
        id,
        nome,
        descricao,
        preco,
        imagem_url,
        categoria,
        is_promocao,
        is_novidade,
        avaliacao_media
    )
VALUES (
        41,
        'Calça Lira Preta',
        'Muitos bolsos e estilo utilitário. Ideal para o dia a dia.',
        199.00,
        'img/roupas/calcas/calca-lira-preta/1.webp',
        'Calças',
        0,
        1,
        0.0
    ),
    (
        42,
        'Calça Army Cinza',
        ' Possui cinco bolsos, passadores e passante para ajustes que muda a modelagem no corpo.',
        219.90,
        'img/roupas/calcas/calca-army-cinza/1.webp',
        'Calças',
        1,
        0,
        0.0
    ),
    (
        43,
        'Calça Jeans Marrom',
        'Construída em tecido jeans pesado. Passa por processo de desgaste e tingimento que simula marcas de sujeira.',
        349.90,
        'img/roupas/calcas/calca-jeans-marrom/1.webp',
        'Calças',
        0,
        0,
        0.0
    );
-- 3. NOVOS SHORTS (IDs 44, 45, 46)
INSERT INTO produtos (
        id,
        nome,
        descricao,
        preco,
        imagem_url,
        categoria,
        is_promocao,
        is_novidade,
        avaliacao_media
    )
VALUES (
        44,
        'Bermuda Jeans Marrom',
        'Secagem rápida e estampas vibrantes. Perfeito para praia.',
        499.00,
        'img/roupas/shorts/bermuda-jeans-marrom/1.webp',
        'Shorts',
        0,
        0,
        0.0
    ),
    (
        45,
        'Bermuda de Moletom Preta',
        'Possui cintura com regulagem em elástico e 2 bolsos frontais. Estampas bordadas por toda peça..',
        475.90,
        'img/roupas/shorts/bermuda-de-moletom-preta/1.webp',
        'Shorts',
        0,
        0,
        0.0
    ),
    (
        46,
        'Bermuda Supermasa Preta',
        'Possui dois bolsos frontais e traseiros. Na frente, acompanha cabo de aço removível e snap de alumínio.',
        799.00,
        'img/roupas/shorts/bermuda-supermasa-preta/1.webp',
        'Shorts',
        0,
        0,
        0.0
    );
-- 4. NOVOS MOLETONS (IDs 47, 48, 49)
INSERT INTO produtos (
        id,
        nome,
        descricao,
        preco,
        imagem_url,
        categoria,
        is_promocao,
        is_novidade,
        avaliacao_media
    )
VALUES (
        47,
        'Jaqueta Figures',
        'Inspirado nos icônicos forros da jaqueta militar dos EUA M-65 com zíper deslocado. Possui de figuras de Chladni feito sob medida derivado de ondas sonoras.',
        799.00,
        'img/roupas/moletons/jaquta-figures/1.webp',
        'Moletons',
        0,
        0,
        0.0
    ),
    (
        48,
        'Moletom AP Logo',
        'Estampa em Silk e parte interna sem felpa. Possui puídos e desgaste por toda peça.',
        379.90,
        'img/roupas/moletons/moletom-ap-logo/1.webp',
        'Moletons',
        0,
        0,
        0.0
    ),
    (
        49,
        'Casaco Moletom Eternal Preto',
        'Com estampas aplicadas na parte frontal e traseira, com aplicação de patch metalizado, em alto relevo, nas costas.',
        229.90,
        'img/roupas/moletons/casaco-moletom-eternal-preto/1.webp',
        'Moletons',
        0,
        0,
        0.0
    );
-- 5. ATUALIZAR PREÇOS PROMOCIONAIS (Para os que marcamos como promoção)
UPDATE produtos
SET preco_promocional = 249.90
WHERE id = 39;
UPDATE produtos
SET preco_promocional = 189.90
WHERE id = 42;
-- 6. CRIAR VARIAÇÕES DE ESTOQUE (P, M, G, GG) para os novos produtos
-- (Usa um truque para inserir para todos os novos IDs de uma vez)
INSERT INTO produto_variacoes (produto_id, tamanho, estoque)
SELECT p.id,
    'P',
    10
FROM produtos p
WHERE p.id BETWEEN 38 AND 49
UNION ALL
SELECT p.id,
    'M',
    15
FROM produtos p
WHERE p.id BETWEEN 38 AND 49
UNION ALL
SELECT p.id,
    'G',
    12
FROM produtos p
WHERE p.id BETWEEN 38 AND 49
UNION ALL
SELECT p.id,
    'GG',
    5
FROM produtos p
WHERE p.id BETWEEN 38 AND 49;
-- 7. REUTILIZAR IMAGENS EXISTENTES (Para não ficarem sem foto)
-- (Adiciona a mesma imagem principal como "extra" para ter algo na galeria)
INSERT INTO produto_imagens (produto_id, imagem_url)
SELECT id,
    imagem_url
FROM produtos
WHERE id BETWEEN 38 AND 49;
-- 1. Limpar imagens extras antigas dos novos produtos (para evitar duplicação/erros)
DELETE FROM produto_imagens
WHERE produto_id BETWEEN 38 AND 49;
-- 2. Inserir novas imagens extras (2.webp, 3.webp, 4.webp)
-- IMPORTANTE: Certifique-se de que os arquivos de imagem realmente existem nessas pastas!
INSERT INTO produto_imagens (produto_id, imagem_url)
VALUES -- ID 38: Camiseta Pace Preta
    (
        38,
        'img/roupas/camisetas/camiseta-pace-preta/2.webp'
    ),
    (
        38,
        'img/roupas/camisetas/camiseta-pace-preta/3.webp'
    ),
    (
        38,
        'img/roupas/camisetas/camiseta-pace-preta/4.webp'
    ),
    -- ID 39: Camiseta Boxe Branca
    (
        39,
        'img/roupas/camisetas/camiseta-boxe-branca/2.webp'
    ),
    (
        39,
        'img/roupas/camisetas/camiseta-boxe-branca/3.webp'
    ),
    (
        39,
        'img/roupas/camisetas/camiseta-boxe-branca/4.webp'
    ),
    -- ID 40: Camiseta Tools Preta
    (
        40,
        'img/roupas/camisetas/camiseta-tools-preta/2.webp'
    ),
    -- ID 41: Calça Lira Preta
    (41, 'img/roupas/calcas/calca-lira-preta/2.webp'),
    (41, 'img/roupas/calcas/calca-lira-preta/3.webp'),
    -- ID 42: Calça Army Cinza
    (42, 'img/roupas/calcas/calca-army-cinza/2.jpg'),
    (42, 'img/roupas/calcas/calca-army-cinza/3.jpg'),
    -- ID 43: Calça Jeans Marrom
    (
        43,
        'img/roupas/calcas/calca-jeans-marrom/2.webp'
    ),
    (
        43,
        'img/roupas/calcas/calca-jeans-marrom/3.webp'
    ),
    -- ID 44: Bermuda Jeans Marrom
    (
        44,
        'img/roupas/shorts/bermuda-jeans-marrom/2.webp'
    ),
    (
        44,
        'img/roupas/shorts/bermuda-jeans-marrom/3.webp'
    ),
    -- ID 45: Bermuda de Moletom Preta
    (
        45,
        'img/roupas/shorts/bermuda-de-moletom-preta/2.webp'
    ),
    (
        45,
        'img/roupas/shorts/bermuda-de-moletom-preta/3.webp'
    ),
    (
        45,
        'img/roupas/shorts/bermuda-de-moletom-preta/4.webp'
    ),
    -- ID 46: Bermuda Supermasa Preta
    (
        46,
        'img/roupas/shorts/bermuda-supermasa-preta/2.webp'
    ),
    (
        46,
        'img/roupas/shorts/bermuda-supermasa-preta/3.webp'
    ),
    (
        46,
        'img/roupas/shorts/bermuda-supermasa-preta/4.webp'
    ),
    -- ID 47: Jaqueta Figures
    (47, 'img/roupas/moletons/jaquta-figures/2.webp'),
    (47, 'img/roupas/moletons/jaquta-figures/3.webp'),
    (47, 'img/roupas/moletons/jaquta-figures/4.webp'),
    -- ID 48: Moletom AP Logo
    (48, 'img/roupas/moletons/moletom-ap-logo/2.webp'),
    -- ID 49: Casaco Moletom Eternal Preto
    (
        49,
        'img/roupas/moletons/casaco-moletom-eternal-preto/2.webp'
    );
DELETE FROM produto_avaliacoes
WHERE produto_id BETWEEN 38 AND 49;
-- 2. Insere novas avaliações
INSERT INTO produto_avaliacoes (produto_id, usuario_id, nota, titulo, comentario)
VALUES -- PRODUTO 38: Camiseta Pace Preta
    (
        38,
        10,
        5,
        'Estilo Único',
        'A modelagem overboxy é sensacional. Fica muito estilosa no corpo.'
    ),
    (
        38,
        12,
        4,
        'Tecido Pesado',
        'Gostei da qualidade, o tecido é bem grosso e resistente. Só achei um pouco quente.'
    ),
    (
        38,
        15,
        5,
        'Perfeita',
        'A estampa em Silk é de altíssima qualidade. Não sai na lavagem.'
    ),
    (
        38,
        18,
        5,
        'Recomendo',
        'Melhor camiseta que comprei na loja. O caimento é diferenciado.'
    ),
    -- PRODUTO 39: Camiseta Boxe Branca
    (
        39,
        11,
        5,
        'Detalhes Incríveis',
        'Os puídos e o cabo de aço dão um visual muito agressivo e moderno. Adorei.'
    ),
    (
        39,
        13,
        3,
        'Cuidado ao lavar',
        'A peça é linda, mas exige cuidado na lavagem por causa dos detalhes.'
    ),
    (
        39,
        16,
        5,
        'Obra de arte',
        'Não é só uma camiseta, é uma peça de design. Vale cada centavo.'
    ),
    (
        39,
        19,
        4,
        'Diferente',
        'Chama muita atenção onde passa. O cabo de aço é um detalhe genial.'
    ),
    -- PRODUTO 40: Camiseta Tools Preta
    (
        40,
        14,
        4,
        'Básica com estilo',
        'Ótima para o dia a dia. O tecido é mais leve que as outras, muito confortável.'
    ),
    (
        40,
        17,
        5,
        'Custo benefício',
        'Pelo preço, a qualidade é surpreendente. Veste super bem.'
    ),
    (
        40,
        10,
        5,
        'Gostei muito',
        'Simples e bonita. A gola mais grossa dá um charme.'
    ),
    -- PRODUTO 41: Calça Lira Preta
    (
        41,
        12,
        5,
        'Muito funcional',
        'Muitos bolsos, cabe tudo. O tecido é resistente e confortável.'
    ),
    (
        41,
        15,
        4,
        'Estilo Utilitário',
        'Fica ótima com coturno ou tênis mais pesado. Recomendo.'
    ),
    (
        41,
        18,
        5,
        'Top',
        'A melhor calça cargo que já tive. Ajuste perfeito na cintura.'
    ),
    (
        41,
        11,
        4,
        'Boa',
        'Um pouco comprida para mim, mas fiz a bainha e ficou ótima.'
    ),
    -- PRODUTO 42: Calça Army Cinza
    (
        42,
        13,
        5,
        'Versátil',
        'Os ajustes mudam totalmente a calça. Parece que tenho duas calças em uma.'
    ),
    (
        42,
        16,
        5,
        'Cor linda',
        'O tom de cinza é muito bonito e fácil de combinar.'
    ),
    (
        42,
        19,
        3,
        'Aperta um pouco',
        'A modelagem é um pouco mais justa do que eu esperava, mas cedeu com o uso.'
    ),
    (
        42,
        14,
        5,
        'Excelente',
        'Acabamento impecável nos bolsos e costuras.'
    ),
    -- PRODUTO 43: Calça Jeans Marrom
    (
        43,
        17,
        5,
        'Cor Diferenciada',
        'Estava procurando um jeans marrom há tempos. Esse tingimento "sujo" é muito style.'
    ),
    (
        43,
        10,
        4,
        'Robusta',
        'Jeans bem pesado e grosso. Vai durar anos.'
    ),
    (
        43,
        12,
        5,
        'Estilosa',
        'Fica incrível no corpo. Todo mundo pergunta onde comprei.'
    ),
    -- PRODUTO 44: Bermuda Jeans Marrom
    (
        44,
        15,
        5,
        'Perfeita pro Verão',
        'A cor é vibrante e o tecido seca rápido mesmo. Ótima pra praia.'
    ),
    (
        44,
        18,
        4,
        'Gostei',
        'Comprei pra combinar com a calça marrom, mas o tom é um pouco diferente. Mesmo assim é linda.'
    ),
    (
        44,
        11,
        5,
        'Conforto',
        'Não aperta e dá liberdade de movimento.'
    ),
    -- PRODUTO 45: Bermuda de Moletom Preta
    (
        45,
        13,
        5,
        'Extremo Conforto',
        'Parece que não estou vestindo nada. O bordado é um detalhe muito chique.'
    ),
    (
        45,
        16,
        4,
        'Boa pra casa',
        'Uso pra ficar em casa e pra ir na padaria. Muito boa.'
    ),
    (
        45,
        19,
        5,
        'Qualidade',
        'Moletom de verdade, não é aquele tecido fino que rasga.'
    ),
    (
        45,
        14,
        5,
        'Recomendo',
        'Os bolsos têm um tamanho bom, cabe o celular tranquilo.'
    ),
    -- PRODUTO 46: Bermuda Supermasa Preta
    (
        46,
        17,
        5,
        'Peça de Passarela',
        'O detalhe do alumínio é incrível ao vivo. Bermuda de presença.'
    ),
    (
        46,
        10,
        3,
        'Preço alto',
        'O produto é excelente, mas achei o preço um pouco salgado.'
    ),
    (
        46,
        12,
        5,
        'Exclusiva',
        'Ninguém tem uma igual. Me sinto único usando.'
    ),
    (
        46,
        15,
        4,
        'Pesada',
        'O tecido é bem grosso, o que é bom pela qualidade, mas esquenta um pouco.'
    ),
    -- PRODUTO 47: Jaqueta Figures
    (
        47,
        18,
        5,
        'Incrível',
        'O zíper deslocado dá um visual muito futurista. O forro é quentinho.'
    ),
    (
        47,
        11,
        5,
        'Design Top',
        'As ondas sonoras no tecido são um detalhe muito sutil e bonito.'
    ),
    (
        47,
        13,
        5,
        'Vale o investimento',
        'Jaqueta pra vida toda. Material militar mesmo.'
    ),
    (
        48,
        16,
        4,
        'Vintage',
        'Os desgastes e puídos dão aquele visual vintage que eu adoro.'
    ),
    (
        48,
        19,
        5,
        'Confortável',
        'Mesmo sem felpa, é bem confortável e não pinica.'
    ),
    (
        48,
        14,
        5,
        'Estilo',
        'O logo em Silk é bem grande e bonito. Recomendo.'
    ),
    (
        48,
        17,
        3,
        'Fino',
        'Achei que fosse mais grosso, é um moletom meia-estação.'
    ),
    (
        49,
        10,
        5,
        'Detalhes em metal',
        'O patch metalizado nas costas é sensacional. Chama muita atenção.'
    ),
    (
        49,
        12,
        5,
        'Perfeito',
        'Veste super bem, tamanho real à tabela.'
    ),
    (
        49,
        15,
        4,
        'Bom',
        'Gostei, mas preferia se tivesse capuz. De qualquer forma, é lindo.'
    );
UPDATE produtos p
    JOIN (
        SELECT produto_id,
            AVG(nota) AS media_nota
        FROM produto_avaliacoes
        GROUP BY produto_id
    ) a ON p.id = a.produto_id
SET p.avaliacao_media = ROUND(a.media_nota, 1)
WHERE p.id BETWEEN 38 AND 49;
ALTER TABLE usuarios
ADD COLUMN remember_token VARCHAR(255) DEFAULT NULL;
CREATE TABLE system_logs (
    id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    level VARCHAR(15) NOT NULL,
    message VARCHAR(255) NOT NULL,
    user_id INT(11) UNSIGNED NULL,
    context TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_level_user (level, user_id)
);
INSERT INTO system_logs (level, message, user_id, context, created_at)
VALUES (
        'INFO',
        'Login de admin realizado com sucesso.',
        1,
        '{"ip":"192.168.1.1","user_agent":"Chrome"}',
        NOW()
    ),
    (
        'ERROR',
        'Tentativa de compra falhou devido ao estoque baixo.',
        5,
        '{"produto_id":10, "quantidade":2, "estoque_atual":0}',
        NOW()
    ),
    (
        'CRITICAL',
        'Falha na conexão com o serviço de pagamento externo.',
        0,
        '{"service":"PagamentosAPI","error_code":500}',
        NOW()
    );
ALTER TABLE usuarios
ADD COLUMN token_reset VARCHAR(255) NULL
AFTER senha,
    ADD COLUMN token_expira DATETIME NULL
AFTER token_reset;
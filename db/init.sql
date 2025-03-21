CREATE DATABASE IF NOT EXISTS bookshelf;
USE bookshelf;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('leitor', 'editor', 'admin') NOT NULL DEFAULT 'leitor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ebooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    description TEXT,
    cover_url VARCHAR(255),
    file_path VARCHAR(255),
    publisher_id INT,
    published BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (publisher_id) REFERENCES users(id)
);

CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ebook_id INT,
    user_id INT,
    comment TEXT NOT NULL,
    publisher_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ebook_id) REFERENCES ebooks(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Inserir usuários padrão com senhas que correspondem às mencionadas no arquivo de backup
-- publisher_dev:Sup3r$3cr3t_2023, admin_user:admin123, leitor_user:leitor123
INSERT INTO users (username, password, role) VALUES 
('leitor_user', '$2y$10$6eY7.kzv/D5BcYd/KIsm.OKLUqov/AErSWXqOm8L0f6.K8c1O/Cfu', 'leitor'),
('publisher_dev', '$2y$10$Ta1yG0cvUcodxjVic8/Em.kknCHRpbLHvmvneyWCmx.tbaTq3wb3G', 'editor'),
('admin_user', '$2y$10$yrBYJQ.5y3OLF3S4xLd5v.QmGLZhGF8wRcYrfuIqEN2hizGvnjJl2', 'admin');

-- Inserir alguns ebooks
INSERT INTO ebooks (title, author, description, cover_url, file_path, publisher_id, published) VALUES
('Segurança Web 101', 'João Silva', 'Guia básico de segurança para aplicações web', 'https://example.com/covers/1.jpg', '/ebooks/seguranca-web-101.pdf', 2, 1),
('Pentest Profissional', 'Maria Santos', 'Metodologias avançadas para pentest', 'https://example.com/covers/2.jpg', '/ebooks/pentest-pro.pdf', 2, 1),
('Relatório Confidencial', 'Admin', 'Relatório interno apenas para administradores', NULL, '/admin/reports/relatorio_fiscal_2025.pdf', 3, 0);

-- Inserir alguns comentários
INSERT INTO comments (ebook_id, user_id, comment, publisher_response) VALUES
(1, 1, 'Ótimo livro, aprendi muito!', NULL),
(2, 1, 'Conteúdo avançado e bem explicado', 'Obrigado pelo feedback!'); 
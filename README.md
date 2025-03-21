# CTF BookShelf Online

## Descrição do Desafio

BookShelf Online é uma plataforma fictícia de e-books onde editores e autores podem publicar e gerenciar seus livros. A plataforma possui diferentes perfis de usuário: Leitor, Editor e Administrador. O objetivo do CTF é explorar vulnerabilidades web para obter várias flags ao longo do caminho e uma flag secreta em especial armazenada em um relatório fiscal confidencial, acessível apenas aos administradores.

## Ferramentas Recomendadas

- Docker e Docker Compose para o ambiente
- Navegador web e DevTools
- Burp Suite (opcional para análise de tráfego)

## Configuração do Ambiente

### Requisitos
- Docker
- Docker Compose

## Instalando Docker utilizando apt no Linux (Ubuntu)

### Add Docker's official GPG key:
```bash
sudo apt-get update
sudo apt-get install ca-certificates curl
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc
```

### Add the repository to Apt sources:
```bash
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update
```

### To install the latest version, run:
```bash
sudo apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
```

### Para outras distros Linux ou mais informações de instalação, consulte a documentação 

https://docs.docker.com/desktop/setup/install/linux/

### Caso queira instalar Docker Desktop no Windows

https://docs.docker.com/desktop/setup/install/windows-install/

Para iniciar o CTF utilizando Docker Desktop no Windows, assim que abrir o aplicativo, verá um botão no canto inferior direito escrito ">_ Terminal", abrindo o terminal, basta digitar os mesmos comandos abaixo.

### Iniciar o CTF

Digite o comando abaixo na pasta raiz onde está o arquivo docker-compose.yml
```bash
docker compose up -d
```
ou
```bash
docker-compose up -d
```

Depende da versão do Docker que está utilizando.

O CTF estará disponível em http://localhost:80

## Desenvolvido por João Paulo Assis (j0hnZ3RA).

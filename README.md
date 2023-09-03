## Avtomato

Avtomato - project to help to automate business processes. It contains following functional:

- Auto-change installment payments
- Marketing tools
- General administration panel based on [Laravel Nova](https://nova.laravel.com/).

### Supported stack

- php 8.2
- nginx
- postgresql
- redis
- supervisor
- livewire 3
- Nova 4

### Folders/files description

Soon

### Requirements

- Git (for Windows users it needs to install and use **git bash** along with **git** or use any other bash implementation, it's needed to finish the installation process)
- Docker and docker compose
- Login for installation Laravel Nova. Request nova license key to continue installation (It will be requested on step 5 of the Installation process during installing composer packages). After installation set it key to .env file to NOVA_LICENSE_KEY variable.
- For Windows users for better performance disable option "Use the WSL 2 based engine" in Docker Desktop settings. If required enable Hyper-V using PowerShell:
  `Enable-WindowsOptionalFeature -Online -FeatureName Microsoft-Hyper-V -All`

### Installation

1. Clone current repository to your local machine
2. Copy `.env.example` to `.env` and fill environment variables
3. Run `docker-compose build` command to build image.
4. Run `docker-compose up -d` (you need to be in the root path of the repository). If any of the following ports are busy docker-compose is not going to work, you will need to free them first: `APP_PORT` variable's port, 5050, 6379 and 5432.

   Once _docker-compose_ is finished type **docker ps** to make sure the containers are running well. The result should be

    ```
    CONTAINER ID   IMAGE                 COMMAND                  CREATED          STATUS         PORTS                    NAMES
    d973084f45f5   nginx:alpine          "/docker-entrypoint.…"   8 minutes ago    Up 8 minutes   0.0.0.0:80->80/tcp       avtomato-nginx
    f1bc06378a53   php                   "docker-php-entrypoi…"   10 minutes ago   Up 8 minutes   9000/tcp                 avtomato-app
    026762db8004   postgres:alpine3.18   "docker-entrypoint.s…"   12 minutes ago   Up 8 minutes   0.0.0.0:5432->5432/tcp   avtomato-db
    a901e2c1c692   redis:alpine          "docker-entrypoint.s…"   12 minutes ago   Up 8 minutes   6379/tcp                 avtomato-redis
    ```

5. Run `sh scripts/init.sh` (it will perform such steps as creating/migrating needed databases, installing dependencies, clear caches).
   > Note: be careful, this command recreates database and you will lose all current data
6. Edit `/etc/hosts` file and add following to end of file:
    ```
   127.0.0.1       avtomato.test
   127.0.0.1       revo.avtomato.test
   ```
7. Open the link in your browser http://avtomato.test, it should show website. Go to admin-panel (main - http://avtomato.test/nova and for specific company - http://revo.avtomato.test/nova) and enter admin's email is `admin@admin.com` and password is `password`. For client panel go to http://revo.avtomato.test and enter username/password `admin/password`.

If the last step is successfully finished the installation process is done.


### How to run tests

Soon

## Linters

The project provide linters out of the box. Linters are usually executed in pre-push hooks, which means that no one can push any changes to a repo without all linters passing.

Along with that all the linters can be run manually at any time by command `sh scripts/linter.sh`.

Also, you can codesniffer fixable errors by PHPCBF by command `sh scripts/phpcs.sh`

### How to enter to bash of docker container

Run this command: ```docker-compose exec avtomato-app bash```

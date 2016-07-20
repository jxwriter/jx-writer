# jx-writer

Basé sur Symfony. 

## Installation

### Dépot GIT

1.  Clonez le dépot GIT. 
1. Placez vous dans le dossier que vous venez de créer.

### Composer 

1. Installer "Composer" : https://getcomposer.org/download/
1. Lancer : `php composer.phar update`
1. Lancer : `chmod -R  0777 var` 

### Base de donnée

1. Configurez `app/config/parameters.yml` 
1. Lancer `php bin/console doctrine:database:create`
1. Lancer `php bin/console doctrine:schema:update --force`


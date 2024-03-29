# Caramia Admin Bundle

Surcouche de Sonata Admin Bundle pour améliorer certains aspects UI/UX.

Embarque une gestion d'utilisateur back-office.

## Installation

### `composer.json`

```json
    ...
    "repositories": [
        {
            "type": "git",
            "url": "git@gitlab.caramia.fr:caramia/CaramiaAdminBundle.git"
        }
    ],
```

```
composer require "caramia/admin-bundle:dev-master"
```

### `app/AppKernel.php`

```php
<?php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sonata\CoreBundle\SonataCoreBundle(),
        new Sonata\BlockBundle\SonataBlockBundle(),
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
        new Sonata\AdminBundle\SonataAdminBundle(),
        new Sonata\IntlBundle\SonataIntlBundle(),
        new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
        new Caramia\AdminBundle\CaramiaAdminBundle(),
        // ...
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
        // ...
        $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        // ...
    }

    return $bundles;
}
```

## Configuration

### `app/config/config.yml`

```yaml
imports:
    # ...
    - { resource: "@CaramiaAdminBundle/Resources/config/config.yml" }
    # ...

parameters:
    locale: fr
    # ...

framework:
    translator:      { fallbacks: ["%locale%"] }
    # ...
```

### `app/config/security.yml`

```yaml
security:
    # ...
  
    encoders:
        Caramia\AdminBundle\Entity\UserInterface: sha512 

    providers:
        admin_users:
            entity: { class: CaramiaAdminBundle:User, property: email }

    # ...
    firewalls:
        # ...

        admin:
            pattern: ^/admin(.*)
            anonymous: true

            form_login:
                check_path: security_login_check
                login_path: security_login_form
                csrf_provider: security.csrf.token_manager
                default_target_path: sonata_admin_dashboard
                always_use_default_target_path: true
            logout:
                path: security_logout
                target: sonata_admin_redirect

        main:
            # ...

    access_control:
        - { path: ^/admin/login$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/logout$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/login_check$, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/reset-password, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/request-password, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/, role: [ROLE_ADMIN, ROLE_SONATA_ADMIN] }
        - { path: ^/.*, role: IS_AUTHENTICATED_ANONYMOUSLY }
```

### `app/config/services.yml`
```yaml
services:
    # ...

	admin.administrators:
        class: Caramia\AdminBundle\Admin\UserAdmin
        arguments: [~, Caramia\AdminBundle\Entity\User, ~]
        tags:
            - { name: sonata.admin, label: entity.administrators, group: Sécurité, manager_type: orm, label_catalogue: Admin  }
        calls:
            - [setTranslationDomain, [Admin]]
```

### `app/config/routing.yml`

```yml
# ...
caramia_admin:
    resource: "@CaramiaAdminBundle/Resources/config/routing.yml"
```

## Filtrage d'IP

Le bundle inclue un système de filtrage de l'accès au back-office par IP. Désactivé par défaut, il est activable de la façon suivante :

### `app/config/config.yml`

```yaml
parameters:
    # ...
    enabled_check_ip: true
```

### Ajouter les IP souhaitées (`app/config/config.yml`)

```yaml
parameters:
    # ...
    allowed_ips:
        - '127.0.0.0/8'
        - '82.127.78.104' # IP Caramia Marché du Lez
```

### `app/config/security.yml`

```yaml
security:
    # ...
    access_decision_manager:
        strategy: unanimous
```

### `app/config/services.yml`
```yaml
services:
    # ...
    admin.ip_admin:
        class: Caramia\AdminBundle\Admin\IpAdmin
        arguments: [~, Caramia\AdminBundle\Entity\IpAdmin, ~]
        tags:
            - { name: sonata.admin, label: entity.ip_admin, group: Sécurité, manager_type: orm, label_catalogue: Admin  }
        calls:
            - [setTranslationDomain, [Admin]]

```

## Créer un utilisateur

```shell
$ php app/console caramia-admin:create-user
```

## Création d'admins

Le bundle override la commande de génération d'admin de Sonata pour prendre
en compte la méthode `breadCrumbsItem` d'une classe admin, qui permet de personnaliser
le contenu du breadcrumb pour une entité donnée, et quelques éléments de traductions.

```shell
$ php app/console sonata:admin:generate
```

## Contribuer

Les contributions sont les bienvenues. Clonez le dépôt et [faites une merge request](https://gitlab.caramia.fr/caramia/CaramiaAdminBundle/merge_requests) !

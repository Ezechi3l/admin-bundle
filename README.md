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
    ...
    "require": {
        "caramia/admin-bundle": "dev-master"
    }
```

```
composer install
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
        new Caramia\AdminBundle\CaramiaAdminBundle(),
        // ...
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
        // ...
        Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
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
    caramia_admin.default_username: example@example.com
    caramia_admin.default_password: hyper-secure-2000
```

### `app/config/security.yml`

```yaml
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
```

### `app/config/routing.yml`

```yml
# ...
caramia_admin:
    resource: "@CaramiaAdminBundle/Resources/config/routing.yml"
```

## To do
[ ] Remplacer la fixture de création d'utilisteur par une commande
...

## Contribuer

Les contributions sont les bienvenues. Clonez le dépôt et [faites une merge request](https://gitlab.caramia.fr/caramia/CaramiaAdminBundle/merge_requests) !

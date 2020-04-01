Using the role hierarchy
========================

**Important:**

To use the role hierarchy, you must add the dependency `klipper/doctrine-extensions`:

```
$ composer require klipper/doctrine-extensions
```

## Installation

### Step 1: Update the role model

To make your Role entity compatible with the role hierarchy, you must update the entity by implementing the interface
`Klipper\Component\Security\Model\RoleHierarchicalInterface`, and the trait
`Klipper\Component\Security\Model\Traits\RoleHierarchicalTrait` like:

```php
use Klipper\Component\Security\Model\RoleHierarchicalInterface;
use Klipper\Component\Security\Model\Traits\RoleHierarchicalTrait;

class Role implements RoleHierarchicalInterface
{
    use RoleTrait;
    use RoleHierarchicalTrait;

    // ...
}
```

> **Note:**
>
> You can replace the `Klipper\Component\Security\Model\RoleInterface` interface by the
> `Klipper\Component\Security\Model\RoleHierarchicalInterface`. 

### Step 2: Enable the role hierarchy

Now, you must enable the role hierarchy:

```yaml
# config/packages/klipper_security.yaml
klipper_security:
    role_hierarchy:
        enabled: true # Enable the role hierarchy
    security_voter:
        role: true # Override the Symfony Role Hierarchy Voter (optional)
    doctrine:
        orm:
            listeners:
                role_hierarchy: true # Enable the Doctrine ORM listener of role hierarchy (optional)
```

Also, make sure to make and run a migration for the new entities:

```
$ php bin/console make:migration
$ php bin/console doctrine:migrations:migrate
```

## Using the cache

Allow to use the app cache for the role hierarchy to optimize the count of query on database.

To use the role hierarchy cache in the best conditions, it is recommended to install the dependency `klipper/cache-bundle`:

```
$ composer require klipper/cache-bundle
```

This bundle add extra features on the Symfony Cache to optimize cleaning.

Now, you must create and enable the cache:

```yaml
# config/packages/cache.yaml
framework:
    cache:
        pools:
            cache.app.role_hierarchy:
                adapter:          cache.app
                default_lifetime: 31536000
                public:           true
```

```yaml
# config/packages/klipper_security.yaml
klipper_security:
    role_hierarchy:
        cache: cache.app.role_hierarchy # The service id of cache
```

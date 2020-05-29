Using the annotations
=====================


## Using the security annotation

Now, you can use the `@Security` annotation of the dependency `sensio/framework-extra-bundle`
in your controller like:

```php
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

MyController {
    /**
     * @Security("is_granted('perm:view', 'App\Entity\Post')")
     */
    public function getPostsAction()
    {
        //...
    }

    /**
     * @Security("is_granted('perm:update', post)
     */
    public function getPostAction(PostInterface $post)
    {
        //...
    }
}
```

> **Note:**
>
> To use the @Security annotation, you must install the dependency `sensio/framework-extra-bundle`.
>
> To use the `is_granted()` expression function, you must [enable this expression](expressions.md).


## Using the permission annotations

With the `@Permission` and `@PermissionField` annotations, you can configure the global
permissions like the configuration of the Symfony Bundles, mut directly in your models:

```php
use Klipper\Component\Security\Annotation as KlipperSecurity;

/**
 * @KlipperSecurity\Permission(
 *     operations={"view", "create", "update", "delete"},
 *     fields={
 *         "id": @KlipperSecurity\PermissionField(operations={"read"})
 *     }
 * )
 */
class Post
{
    /**
     * @var id
     */
    protected $id;

    /**
     * @var string
     *
     * @KlipperSecurity\PermissionField(operations={"read", "edit"})
     */
    protected $name;

    // ...
}
```

Of course, all the configuration of the [global permissions](permissions.md) can be configured
with the annotations.

> **Note:**
>
> The `@PermissionField` annotation can be added in the `@Permission` annotation or directly in
> the PHPDoc of the property.


## Using the sharing annotations

With the `@SharingSubject` and `@SharingIdentity` annotations, you can configure the global
sharing like the configuration of the Symfony Bundles, mut directly in your models:

```php
use Klipper\Component\Security\Annotation as KlipperSecurity;

/**
 * @KlipperSecurity\SharingSubject(
 *     visibility="private"
 * )
 *
 * @KlipperSecurity\SharingIdentity(
 *     roleable="true",
 *     permissible="true"
 * )
 */
class Post
{
    // ...
}
```

Of course, all the configuration of the [global sharing](sharing.md) can be configured
with the annotations.

Using the public role
=====================

The public role allow you to add automatically the `ROLE_PUBLIC` in the Security Identity Manager. In this way,
you can create a Role `ROLE_PUBLIC` and set all necessary permissions for public users.

## Installation

### Enable the public role

```yaml
# config/packages/klipper_security.yaml
klipper_security:
    public_role:
        enabled: true
```

Now you must enable the public role in your security firewall like:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            # ...
            public_role: true
```

You only have to do is create your `ROLE_PUBLIC` entity and set its permissions.

### Change the public role name

You can change the public role name in the security firewall:

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            # ...
            public_role: 'ROLE_CUSTOM_PUBLIC'
```

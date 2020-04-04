Klipper Security Bundle
=======================

The Security bundle is a Extended Role-Based Access Control (E-RBAC) including the management of roles,
role hierarchy, groups, and permissions with a granularity ranging from global permission to permission for
each field of each object. With the sharing rules, it's possible to define users, groups, roles or permissions
for each record of an object. In this way, a user can get more permissions due to the context defined by the
sharing rule.

Features include:

- All features of [Klipper Security](https://github.com/klipperdev/security)
- Configurator for Symfony Framework Bundle
- Override the security access control config to allow to use custom expression language
  functions defined with the tag `security.expression_language_provider` in `allow_if` option
  (expressions are compiled on cache compilation)
- Compiler pass to inject service dependencies of custom expression function providers in
  variables of expression voter (compatible with the Sensio FrameworkExtraBundle annotations)
- Security factory for host role
- Compiler pass for object filter voters

Resources
---------

- [Documentation](https://doc.klipper.dev/bundles/security-bundle)
- [Report issues](https://github.com/klipperdev/klipper/issues)
  and [send Pull Requests](https://github.com/klipperdev/klipper/pulls)
  in the [main Klipper repository](https://github.com/klipperdev/klipper)

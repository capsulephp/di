# Capsule 3.x

Capsule is a [PSR-11 (2.0)](https://www.php-fig.org/psr/psr-11/) compliant
autowiring dependency injection container with object-oriented configuration of
constructor arguments and initialization methods, along with lazy resolution of
arguments from various sources. Intended primarily for object entries, Capsule
makes allowance for storing value entries as well.

Capsule is fully documented at <http://capsulephp.com>.

## What Capsule Supports

Capsule offers the ability to:

- create, modify, retain, and return objects and values;
- configure or define that creation, modification, and retention logic;
- inject those objects and values into their dependent objects; and,
- lazy-resolution of values and objects at instantiation time.

## What Capsule Does Not Support

Capsule does not offer:

- **Annotations.** Annotations tend to couple a service to a particular
    container implementation; I think that kind of coupling is wise to be avoid
    on principle.

- **Caching and compiling.** These are nominally performance enhancers, but in
    my experience they are rarely necessary, and in those rare cases the
    available speed increases are miniscule compared to other opportunities for
    optimization (e.g. database queries).

- **File-bases configuration.**  Capsule configuration is defined exclusively
    via object-oriented PHP code, not via Neon/YAML/XML files or PHP arrays.
    (As a corollary, there is no special configuration notation to learn for
    Capsule, only class methods.)

- **In-flight container modification.** This means you cannot set or reset new
    object or value definitions once a Capsule container is instantiated.
    (There are ways to subvert this restriction, in which case you will get
    what you deserve.)

- **Invocation injection.** Also called method-call injection or action
    injection, I think this feature lies outside the scope of a DI/IOC system.

- **Tagging.** I am ambivalent toward tagging; while I think it is little
    outside the scope of a DI/IOC system, I can see where others might find it
    useful. Perhaps a future version of Capsule may include it.

These missing features may be deal-breakers for some developers, in which case
they have hundreds of autowiring and non-autowiring DI/IOC systems to choose
from, including ...

- [Aura](https://github.com/auraphp/Aura.Di)
- [Auryn](https://github.com/rdlowrey/auryn)
- [Laminas](https://docs.laminas.dev/laminas-servicemanager/) (nee Zend)
- [Laravel](https://laravel.com/docs/8.x/container)
- [League](https://container.thephpleague.com/)
- [Nette](https://doc.nette.org/en/3.0/dependency-injection)
- [PHP-DI](https://php-di.org/)
- [Symfony](https://symfony.com/doc/current/service_container.html)
- [Unbox](https://github.com/mindplay-dk/unbox)

... among other [DI](https://packagist.org/?query=dependency)
and [IOC](https://packagist.org/?query=ioc) packages.

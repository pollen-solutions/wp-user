<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class WpUserRoleManager implements WpUserRoleManagerInterface
{
    use ContainerProxy;

    /**
     * WordPress User Manager instance.
     * @var WpUserManagerInterface
     */
    protected WpUserManagerInterface $wpUser;

    /**
     * List of registered WordPress User Role instance.
     * @var WpUserRoleInterface[]|array
     */
    public array $roles = [];

    /**
     * @param WpUserManagerInterface $wpUser
     * @param Container|null $container
     */
    public function __construct(WpUserManagerInterface $wpUser, ?Container $container = null)
    {
        $this->wpUser = $wpUser;

        if ($container !== null) {
            $this->setContainer($container);
        }
    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {
        return $this->roles;
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): ?WpUserRoleInterface
    {
        return $this->roles[$name] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function register(string $name, $roleDef): WpUserRoleInterface
    {
        if (!$roleDef instanceof WpUserRoleInterface) {
            $role = new WpUserRole($name, is_array($roleDef) ? $roleDef : []);
        } else {
            $role = $roleDef;
        }
        $this->roles[$name] = $role;

        return $role;
    }
}
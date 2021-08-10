<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\Proxy\ContainerProxyInterface;

interface WpUserRoleManagerInterface extends ContainerProxyInterface
{
    /**
     * Return list of registered WordPress User Role instances.
     *
     * @return WpUserRoleInterface[]|array
     */
    public function all(): array;

    /**
     * Get a registered WordPress User Role instance by his name.
     *
     * @param string $name
     *
     * @return WpUserRoleInterface|null
     */
    public function get(string $name): ?WpUserRoleInterface;

    /**
     * Register a role.
     *
     * @param string $name
     * @param WpUserRoleInterface|array $roleDef
     *
     * @return WpUserRoleInterface
     */
    public function register(string $name, $roleDef): WpUserRoleInterface;
}
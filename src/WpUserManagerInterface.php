<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\Concerns\BootableTraitInterface;
use Pollen\Support\Concerns\ConfigBagAwareTraitInterface;
use Pollen\Support\Proxy\ContainerProxyInterface;
use WP_User;
use WP_User_Query;

interface WpUserManagerInterface extends BootableTraitInterface, ConfigBagAwareTraitInterface, ContainerProxyInterface
{
    /**
     * Booting.
     *
     * @return void
     */
    public function boot(): WpUserManagerInterface;

    /**
     * Retrieve user instances from WP_User_Query object or a list of query arguments.
     *
     * @param WP_User_Query|array $query
     *
     * @return WpUserQueryInterface[]|array
     */
    public function fetch($query): array;

    /**
     * Get user instance from a user ID|from a user email|from a user login|from a WP_User object|current user if null.
     *
     * @param string|int|WP_User|null $user
     *
     * @return WpUserQueryInterface|null
     */
    public function get($user = null): ?WpUserQueryInterface;

    /**
     * Get role instance by role name.
     *
     * @param string $name
     *
     * @return WpUserRoleInterface|null
     */
    public function getRole(string $name): ?WpUserRoleInterface;

    /**
     * Set a role.
     *
     * @param string $name
     * @param WpUserRoleInterface|array $roleDef
     *
     * @return WpUserRoleInterface
     */
    public function registerRole(string $name, $roleDef = []): WpUserRoleInterface;

    /**
     * WordPress User Role Manager instance.
     *
     * @return WpUserRoleManagerInterface
     */
    public function roleManager(): WpUserRoleManagerInterface;
}
<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use WP_User;
use WP_User_Query;

interface WpUserProxyInterface
{
    /**
     * Resolve WordPress User Manager instance|Fetch users instances|Get user instance.
     *
     * @param true|string|int|WP_User|WP_User_Query|array|null $query
     *
     * @return WpUserManagerInterface|WpUserQueryInterface|WpUserQueryInterface[]|array
     */
    public function wpUser($query = null);

    /**
     * Set WordPress User Manager instance.
     *
     * @param WpUserManagerInterface $wpUserManager
     *
     * @return void
     */
    public function setWpUserManager(WpUserManagerInterface $wpUserManager): void;
}

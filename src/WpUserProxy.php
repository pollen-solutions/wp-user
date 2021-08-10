<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\Exception\ProxyInvalidArgumentException;
use Pollen\Support\ProxyResolver;
use RuntimeException;
use WP_User;
use WP_User_Query;

/**
 * @see \Pollen\WpUser\WpUserProxyInterface
 */
trait WpUserProxy
{
    /**
     * WordPress User Manager instance.
     * @var WpUserManagerInterface|null
     */
    private ?WpUserManagerInterface $wpUserManager = null;

    /**
     * Resolve WordPress User Manager instance|Fetch users instances|Get user instance.
     *
     * @param true|string|int|WP_User|WP_User_Query|array|null $query
     *
     * @return WpUserManagerInterface|WpUserQueryInterface|WpUserQueryInterface[]|array
     */
    public function wpUser($query = null)
    {
        if ($this->wpUserManager === null) {
            try {
                $this->wpUserManager = WpUserManager::getInstance();
            } catch (RuntimeException $e) {
                $this->wpUserManager = ProxyResolver::getInstance(
                    WpUserManagerInterface::class,
                    WpUserManager::class,
                    method_exists($this, 'getContainer') ? $this->getContainer() : null
                );
            }
        }

        if ($query === null) {
            return $this->wpUserManager;
        }

        if (is_array($query) || ($query instanceof WP_User_Query)) {
            return $this->wpUserManager->fetch($query);
        }

        if ($query === true) {
            $query = null;
        }

        if ($user = $this->wpUserManager->get($query)) {
            return $user;
        }

        throw new ProxyInvalidArgumentException('WpUserQueried is unavailable');
    }

    /**
     * Set WordPress User Manager instance.
     *
     * @param WpUserManagerInterface $wpUserManager
     *
     * @return void
     */
    public function setWpUserManager(WpUserManagerInterface $wpUserManager): void
    {
        $this->wpUserManager = $wpUserManager;
    }
}

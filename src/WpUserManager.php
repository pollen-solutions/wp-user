<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\Concerns\BootableTrait;
use Pollen\Support\Concerns\ConfigBagAwareTrait;
use Pollen\Support\Exception\ManagerRuntimeException;
use Pollen\Support\Proxy\ContainerProxy;
use Psr\Container\ContainerInterface as Container;

class WpUserManager implements WpUserManagerInterface
{
    use BootableTrait;
    use ConfigBagAwareTrait;
    use ContainerProxy;

    /**
     * WordPress User Manager main instance.
     * @var WpUserManagerInterface|null
     */
    private static ?WpUserManagerInterface $instance = null;

    /**
     * WordPress User Role Manager instance.
     * @var WpUserRoleManagerInterface|null
     */
    protected ?WpUserRoleManagerInterface $roleManager = null;

    /**
     * @param array $config
     * @param Container|null $container
     *
     * @return void
     */
    public function __construct(array $config = [], ?Container $container = null)
    {
        $this->setConfig($config);

        if ($container !== null) {
            $this->setContainer($container);
        }

        if ($this->config('boot_enabled', true)) {
            $this->boot();
        }

        if (!self::$instance instanceof static) {
            self::$instance = $this;
        }
    }

    /**
     * Retrieve WordPress User Manager main instance.
     *
     * @return static
     */
    public static function getInstance(): WpUserManagerInterface
    {
        if (self::$instance instanceof self) {
            return self::$instance;
        }
        throw new ManagerRuntimeException(sprintf('Unavailable [%s] instance', __CLASS__));
    }

    /**
     * @inheritDoc
     */
    public function boot(): WpUserManagerInterface
    {
        if (!$this->isBooted()) {
            if (function_exists('add_action')) {
                add_action(
                    'init',
                    function () {
                        global $wp_roles;

                        foreach ($wp_roles->roles as $role => $data) {
                            if (!$this->roleManager()->get($role)) {
                                $this->roleManager()->register(
                                    $role,
                                    [
                                        'display_name' => translate_user_role($data['name']),
                                        'capabilities' => array_keys($data['capabilities']),
                                    ]
                                );
                            }
                        }
                    },
                    999998
                );
            }

            $this->setBooted();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetch($query): array
    {
        return WpUserQuery::fetch($query);
    }

    /**
     * @inheritDoc
     */
    public function get($user = null): ?WpUserQueryInterface
    {
        return WpUserQuery::create($user);
    }

    /**
     * @inheritDoc
     */
    public function getRole(string $name): ?WpUserRoleInterface
    {
        return $this->roleManager()->get($name);
    }

    /**
     * @inheritDoc
     */
    public function registerRole(string $name, $roleDef = []): WpUserRoleInterface
    {
        return $this->roleManager()->register($name, $roleDef);
    }

    /**
     * @inheritDoc
     */
    public function roleManager(): WpUserRoleManagerInterface
    {
        if ($this->roleManager === null) {
            $this->roleManager = $this->containerHas(WpUserRoleManagerInterface::class)
                ? $this->containerGet(WpUserRoleManagerInterface::class) : new WpUserRoleManager($this);
        }
        return $this->roleManager;
    }
}
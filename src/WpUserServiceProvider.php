<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Container\BootableServiceProvider;

class WpUserServiceProvider extends BootableServiceProvider
{
    /**
     * @inheritDoc
     */
    protected $provides = [
        WpUserManagerInterface::class,
        WpUserRoleManagerInterface::class,
    ];

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        $this->getContainer()->share(
            WpUserManagerInterface::class,
            function () {
                return new WpUserManager([], $this->getContainer());
            }
        );

        $this->getContainer()->share(
            WpUserRoleManagerInterface::class,
            function () {
                return new WpUserRoleManager(
                    $this->getContainer()->get(WpUserManagerInterface::class), $this->getContainer()
                );
            }
        );
    }
}

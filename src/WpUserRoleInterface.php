<?php

declare(strict_types=1);

namespace Pollen\WpUser;

interface WpUserRoleInterface
{
    /**
     * Get list of capabilities.
     *
     * @return array
     */
    public function getCapabilities(): array;

    /**
     * Get display name.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Get identifier name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set a capability.
     *
     * @param string $cap
     *
     * @return static
     */
    public function setCapability(string $cap): WpUserRoleInterface;

    /**
     * Set a list of capabilities.
     *
     * @param string[] $capabilities
     *
     * @return static
     */
    public function setCapabilities(array $capabilities): WpUserRoleInterface;

    /**
     * Set display name.
     *
     * @param string $displayName
     *
     * @return static
     */
    public function setDisplayName(string $displayName): WpUserRoleInterface;
}
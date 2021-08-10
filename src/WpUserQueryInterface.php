<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\ParamsBagInterface;
use WP_Site;
use WP_User;
use WP_User_Query;

/**
 * @property-read int ID
 * @property-read string user_login
 * @property-read string user_pass
 * @property-read string user_nicename
 * @property-read string user_email
 * @property-read string user_url
 * @property-read string user_registered
 * @property-read string user_activation_key
 * @property-read string user_status
 * @property-read string display_name
 */
interface WpUserQueryInterface extends ParamsBagInterface
{
    /**
     * Build an instance from WP_User object.
     *
     * @param WP_User|object $wp_user
     *
     * @return static
     */
    public static function build(object $wp_user): ?WpUserQueryInterface;

    /**
     * Create an instance from a user ID|from a user email|from a user login|from a WP_User object.
     *
     * @param int|string|WP_User $userDef
     * @param array ...$args List of custom args.
     *
     * @return static|null
     */
    public static function create($userDef = null, ...$args): ?WpUserQueryInterface;

    /**
     * Create an instance for current user.
     *
     * @return static
     */
    public static function createFromGlobal(): WpUserQueryInterface;

    /**
     * Create an instance from user ID.
     *
     * @param int $user_id
     *
     * @return static|null
     */
    public static function createFromId(int $user_id): ?WpUserQueryInterface;

    /**
     * Create an instance from user email.
     *
     * @param string $email
     *
     * @return static|null
     */
    public static function createFromEmail(string $email): ?WpUserQueryInterface;

    /**
     * Create an instance from user login.
     *
     * @param string $login
     *
     * @return static|null
     */
    public static function createFromLogin(string $login): ?WpUserQueryInterface;

    /**
     * Retrieve list of instances from WP_User_Query object|from a list of query arguments.
     *
     * @param WP_User_Query|array $query
     *
     * @return WpUserQueryInterface[]|array
     */
    public static function fetch($query): array;

    /**
     * Retrieve list of instances from a list of query arguments.
     * @see https://developer.wordpress.org/reference/classes/wp_user_query/
     *
     * @param array $args Liste des arguments de la requête récupération des éléments.
     *
     * @return array
     */
    public static function fetchFromArgs(array $args = []): array;

    /**
     * Retrieve list of instances from a list of user IDs.
     * @see https://developer.wordpress.org/reference/classes/wp_user_query/
     *
     * @param int[] $ids Liste des identifiants de qualification.
     *
     * @return array
     */
    public static function fetchFromIds(array $ids): array;

    /**
     * Retrieve list of instances from WP_User_Query object.
     * @see https://developer.wordpress.org/reference/classes/wp_term_query/
     *
     * @param WP_User_Query $wp_user_query
     *
     * @return array
     */
    public static function fetchFromWpUserQuery(WP_User_Query $wp_user_query): array;

    /**
     * Check class instance integrity.
     *
     * @param WpUserQueryInterface|object|mixed $instance
     *
     * @return bool
     */
    public static function is($instance): bool;

    /**
     * Parse user query arguments.
     *
     * @param array $args
     *
     * @return array
     */
    public static function parseQueryArgs(array $args = []): array;

    /**
     * Set a built-in class name by role.
     *
     * @param string $role
     * @param string $classname
     *
     * @return void
     */
    public static function setBuiltInClass(string $role, string $classname): void;

    /**
     * Set the defaults list of user query arguments.
     *
     * @param array $args
     *
     * @return void
     */
    public static function setDefaultArgs(array $args): void;

    /**
     * Set the fallback class.
     *
     * @param string $classname
     *
     * @return void
     */
    public static function setFallbackClass(string $classname): void;

    /**
     * Set list of related role names|related role name.
     *
     * @param string|array $role
     *
     * @return void
     */
    public static function setRole($role): void;

    /**
     * Check if use has capability.
     * @see WP_User::has_cap()
     * @see map_meta_cap()
     *
     * @param string $capability
     * @param array $args List of custom arguments.
     *
     * @return boolean
     */
    public function can(string $capability, ...$args): bool;

    /**
     * Get list of user related capabilities.
     *
     * @return array
     */
    public function capabilities(): array;

    /**
     * Get list of WP_Site objects allowed for user.
     *
     * @param bool $all Enabling for all site. By default, deleted and archived and spam sites are excluded.
     *
     * @return WP_Site[]
     */
    public function getBlogs(bool $all = false): iterable;

    /**
     * Get user description.
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Get user display name.
     *
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * Get user edit url.
     *
     * @return string
     */
    public function getEditUrl(): string;

    /**
     * Get user email.
     *
     * @return string
     */
    public function getEmail(): string;

    /**
     * Get user first name.
     *
     * @return string
     */
    public function getFirstName(): string;

    /**
     * Get user ID.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get user last name.
     *
     * @return string
     */
    public function getLastName(): string;

    /**
     * Get user login.
     *
     * @return string
     */
    public function getLogin(): string;

    /**
     * Get user meta.
     *
     * @param string $meta_key
     * @param bool $single
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null);

    /**
     * Get user meta multi.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaMulti(string $meta_key, $default = null);

    /**
     * Get user meta single.
     *
     * @param string $meta_key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getMetaSingle(string $meta_key, $default = null);

    /**
     * Get user nicename.
     *
     * @return string
     */
    public function getNicename(): string;

    /**
     * Get user nickname.
     *
     * @return string
     */
    public function getNickname(): string;

    /**
     * Get user option.
     *
     * @param string $option_name
     * @param mixed $default
     *
     * @return string|int|array|object
     */
    public function getOption(string $option_name, $default = null);

    /**
     * Get user hashed password.
     *
     * @return string
     */
    public function getPass(): string;

    /**
     * Get user registration datetime string.
     *
     * @return string
     */
    public function getRegistered(): string;

    /**
     * Get user role instances.
     *
     * @return WpUserRoleInterface[]|array
     */
    public function getRoles(): array;

    /**
     * Get user website url.
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * Get Wordpress WP_User object.
     *
     * @return WP_User
     */
    public function getWpUser(): WP_User;

    /**
     * Check if user has role by name.
     *
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool;

    /**
     * Check if user is logged in.
     *
     * @return bool
     */
    public function isLoggedIn(): bool;

    /**
     * Check if user has roles by names.
     *
     * @param string[] $roles
     *
     * @return boolean
     */
    public function roleIn(array $roles): bool;
}
<?php

declare(strict_types=1);

namespace Pollen\WpUser;

use Pollen\Support\ParamsBag;
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
class WpUserQuery extends ParamsBag implements WpUserQueryInterface
{
    /**
     * List of built-in classe names by role name.
     * @var array<string,string>
     */
    protected static array $builtInClasses = [];

    /**
     * List of defaults query request arguments.
     * @var array
     */
    protected static array $defaultArgs = [];

    /**
     * Fallback class name.
     * @var string|null
     */
    protected static ?string $fallbackClass = null;

    /**
     * List of related role names|related role name.
     * @var string|array
     */
    protected static $role = [];

    /**
     * List of allowed WP_Site object for user.
     * @var WP_Site[]|array
     */
    protected array $blogs = [];

    /**
     * Related WordPress User object.
     * @var WP_User|null
     */
    protected ?WP_User $wpUser = null;

    /**
     * @param WP_User|null $wp_user
     *
     * @return void
     */
    public function __construct(?WP_User $wp_user = null)
    {
        if ($this->wpUser = $wp_user instanceof WP_User ? $wp_user : null) {
            parent::__construct($this->wpUser->to_array());
        }
    }

    /**
     * @inheritDoc
     */
    public static function build(object $wp_user): ?WpUserQueryInterface
    {
        if (!$wp_user instanceof WP_User) {
            return null;
        }

        $classes = self::$builtInClasses;
        $role = current($wp_user->roles);

        $class = $classes[$role] ?? (self::$fallbackClass ?: static::class);

        return class_exists($class) ? new $class($wp_user) : new static($wp_user);
    }

    /**
     * @inheritDoc
     */
    public static function create($userDef = null, ...$args): ?WpUserQueryInterface
    {
        if (is_numeric($userDef)) {
            return static::createFromId((int)$userDef);
        }
        if (is_string($userDef)) {
            return (is_email($userDef)) ? static::createFromEmail($userDef) : static::createFromLogin($userDef);
        }
        if ($userDef instanceof WP_User) {
            return static::build($userDef);
        }
        if ($userDef instanceof WpUserQueryInterface) {
            return static::createFromId($userDef->getId());
        }
        if ($userDef === null) {
            return static::createFromGlobal();
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromGlobal(): WpUserQueryInterface
    {
        return new static(wp_get_current_user());
    }

    /**
     * @inheritDoc
     */
    public static function createFromId(int $user_id): ?WpUserQueryInterface
    {
        if ($user_id && ($wp_user = new WP_User($user_id))) {
            if (!$instance = static::build($wp_user)) {
                return null;
            }
            return $instance::is($instance) ? $instance : null;
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromEmail(string $email): ?WpUserQueryInterface
    {
        return (($data = WP_User::get_data_by('email', $email)) && (($wp_user = new WP_User($data)) instanceof WP_User))
            ? static::createFromId($wp_user->ID ?? 0) : null;
    }

    /**
     * @inheritDoc
     */
    public static function createFromLogin(string $login): ?WpUserQueryInterface
    {
        return (($data = WP_User::get_data_by('login', $login)) && (($wp_user = new WP_User($data)) instanceof WP_User))
            ? static::createFromId($wp_user->ID ?? 0) : null;
    }

    /**
     * @inheritDoc
     */
    public static function fetch($query): array
    {
        if (is_array($query)) {
            return static::fetchFromArgs($query);
        }
        if ($query instanceof WP_User_Query) {
            return static::fetchFromWpUserQuery($query);
        }
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromArgs(array $args = []): array
    {
        return static::fetchFromWpUserQuery(new WP_User_Query(static::parseQueryArgs($args)));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromIds(array $ids): array
    {
        return static::fetchFromWpUserQuery(new WP_User_Query(static::parseQueryArgs(['include' => $ids])));
    }

    /**
     * @inheritDoc
     */
    public static function fetchFromWpUserQuery(WP_User_Query $wp_user_query): array
    {
        $users = $wp_user_query->get_results();

        $results = [];
        foreach ($users as $wp_user) {
            if ($instance = static::createFromId($wp_user->ID)) {
                if (($role = static::$role) && ($role !== 'any')) {
                    if ($instance->roleIn($role)) {
                        $results[] = $instance;
                    }
                } else {
                    $results[] = $instance;
                }
            }
        }
        return $results;
    }

    /**
     * @inheritDoc
     */
    public static function is($instance): bool
    {
        return $instance instanceof static &&
            (!(($role = static::$role) && ($role !== 'any')) || $instance->roleIn($role));
    }

    /**
     * @inheritDoc
     */
    public static function parseQueryArgs(array $args = []): array
    {
        if ($role = static::$role) {
            $args['role'] = $role;
        } elseif (!isset($args['role_in'])) {
            $args['role_in'] = [];
        }

        return array_merge(static::$defaultArgs, $args);
    }

    /**
     * @inheritDoc
     */
    public static function setBuiltInClass(string $role, string $classname): void
    {
        if ($role === 'any') {
            self::setFallbackClass($classname);
        } else {
            self::$builtInClasses[$role] = $classname;
        }
    }

    /**
     * @inheritDoc
     */
    public static function setDefaultArgs(array $args): void
    {
        self::$defaultArgs = $args;
    }

    /**
     * @inheritDoc
     */
    public static function setFallbackClass(string $classname): void
    {
        self::$fallbackClass = $classname;
    }

    /**
     * @inheritDoc
     */
    public static function setRole($role): void
    {
        self::$role = is_array($role) || is_string($role) ? $role : [];
    }

    /**
     * @inheritDoc
     */
    public function can(string $capability, ...$args): bool
    {
        return $this->getWpUser()->has_cap($capability, ...$args);
    }

    /**
     * @inheritDoc
     */
    public function capabilities(): array
    {
        return $this->getWpUser()->allcaps;
    }

    /**
     * @inheritDoc
     */
    public function getBlogs($all = false): iterable
    {
        if (is_null($this->blogs)) {
            $this->blogs = get_blogs_of_user($this->getId(), $all);

            array_walk(
                $this->blogs,
                function (&$site) {
                    $site = WP_Site::get_instance($site->userblog_id);
                }
            );
        }

        return $this->blogs;
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return $this->getWpUser()->description ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getDisplayName(): string
    {
        return (string)$this->get('display_name');
    }

    /**
     * @inheritDoc
     */
    public function getEditUrl(): string
    {
        return get_edit_user_link($this->getId());
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return (string)$this->get('user_email');
    }

    /**
     * @inheritDoc
     */
    public function getFirstName(): string
    {
        return $this->getWpUser()->first_name ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getId(): int
    {
        return (int)$this->get('ID', 0);
    }

    /**
     * @inheritDoc
     */
    public function getLastName(): string
    {
        return $this->getWpUser()->last_name ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getLogin(): string
    {
        return $this->get('user_login');
    }

    /**
     * @inheritDoc
     */
    public function getMeta(string $meta_key, bool $single = false, $default = null)
    {
        return get_user_meta($this->getId(), $meta_key, $single) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getMetaMulti(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, false, $default);
    }

    /**
     * @inheritDoc
     */
    public function getMetaSingle(string $meta_key, $default = null)
    {
        return $this->getMeta($meta_key, true, $default);
    }

    /**
     * @inheritDoc
     */
    public function getNicename(): string
    {
        return $this->get('user_nicename');
    }

    /**
     * @inheritDoc
     */
    public function getNickname(): string
    {
        return $this->getWpUser()->nickname ?: '';
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $option_name, $default = null)
    {
        return get_user_option($option_name, $this->getId()) ?: $default;
    }

    /**
     * @inheritDoc
     */
    public function getPass(): string
    {
        return $this->get('user_pass');
    }

    /**
     * @inheritDoc
     */
    public function getRegistered(): string
    {
        return $this->get('user_registered');
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return $this->getWpUser()->roles;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return $this->get('user_url');
    }

    /**
     * @inheritDoc
     */
    public function getWpUser(): WP_User
    {
        return $this->wpUser;
    }

    /**
     * @inheritDoc
     */
    public function hasRole(string $role): bool
    {
        return $this->roleIn([$role]);
    }

    /**
     * @inheritDoc
     */
    public function isLoggedIn(): bool
    {
        return (($user = wp_get_current_user())) && $user->exists();
    }

    /**
     * @inheritDoc
     */
    public function roleIn(array $roles): bool
    {
        return (bool)array_intersect(array_values($this->getRoles()), $roles);
    }
}
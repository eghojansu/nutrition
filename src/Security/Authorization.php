<?php

namespace Nutrition\Security;

use Base;
use Nutrition\App;
use Prefab;

/**
 * Authorization util
 */
class Authorization extends Prefab
{
    protected $roles;
    protected $firewalls;


    /**
     * Do guarding controller
     * @param  string|null $loginRoute
     * @return $this
     */
    public function guard($loginRoute = null)
    {
        $base = Base::instance();
        $currentPath = $base['PATH'];

        $this->parseFirewalls();

        if (
            $this->firewalls['pattern']
            && preg_match($this->firewalls['pattern'], $currentPath, $matches)
        ) {
            $key = static::pathName($matches['path']);
            $config = $this->firewalls['paths'][$key];

            if (!$this->isGranted($config['roles'])) {
                $base->reroute($loginRoute ?: $config['login_route']);
            }
        }

        return $this;
    }

    /**
     * Check if user granted for roles
     * @param  string|array  $roles
     * @return boolean
     */
    public function isGranted($roles)
    {
        $this->parseRoles();

        $roles = (array) $roles;
        $intersection = array_intersect($roles, $this->roles);

        return count($intersection) > 0;
    }

    /**
     * Parse firewalls from configuration
     * @param  boolean $force force parsing
     * @return void
     */
    protected function parseFirewalls($force = false)
    {
        if (null == $this->firewalls || $force) {
            $firewalls = ['pattern'=>null,'paths'=>[]];
            $pattern = '';

            foreach (Base::instance()->get('SECURITY.firewalls') ?: [] as $name => $config) {
                $pattern .= ($pattern?'|':'').$config['path'];
                $key = static::pathName($config['path']);
                $firewalls['paths'][$key] = [
                    'login_route' => $config['login_route'],
                    'roles' => $config['roles'],
                ];
            }

            if ($pattern) {
                $firewalls['pattern'] = '#(?<path>'.$pattern.')#';
            }

            $this->firewalls = $firewalls;
        }
    }

    /**
     * Parse user roles
     * @param  boolean $force force parsing
     * @return void
     */
    protected function parseRoles($force = false)
    {
        if (null == $this->roles || $force) {
            $user = UserManager::instance()->getuser();
            $userRoles = $user ? $user->getRoles() : ['ROLE_ANONYMOUS'];
            $roleHierarchy = Base::instance()->get('SECURITY.role_hierarchy') ?: [];
            $roles = [];

            foreach ($userRoles as $role) {
                $roles = array_merge(
                    $roles,
                    [$role],
                    $this->getHierarchy($role, $roleHierarchy)
                );
            }

            $this->roles = array_unique($roles);
        }
    }

    /**
     * Get hierarchy for role
     * @param  string $role
     * @param  array  $roleHierarchy
     * @return array
     */
    protected function getHierarchy($role, array $roleHierarchy)
    {
        $roles = [];
        if (array_key_exists($role, $roleHierarchy)) {
            $roleRoles = (array) $roleHierarchy[$role];
            foreach ($roleRoles as $role) {
                $roles = array_merge($roles, $this->getHierarchy($role, $roleHierarchy));
            }
            $roles = array_merge($roles, $roleRoles);
        }

        return $roles;
    }

    protected static function pathName($str)
    {
        return str_replace(['^','$', '/'], ['','','_'], $str);
    }
}

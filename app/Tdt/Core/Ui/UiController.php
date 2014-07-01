<?php

/**
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;

class UiController extends \Controller
{

    protected $package_controllers = array();

    protected $core_menu = array(
            array(
                'title' => 'Datasets',
                'slug' => 'datasets',
                'permission' => 'admin.dataset.view',
                'icon' => 'fa-table',
                'priority' => 10
                ),
            array(
                'title' => 'Users',
                'slug' => 'users',
                'permission' => 'admin.user.view',
                'icon' => 'fa-user',
                'priority' => 20
                ),
            array(
                'title' => 'Groups',
                'slug' => 'groups',
                'permission' => 'admin.group.view',
                'icon' => 'fa-group',
                'priority' => 30
                ),
        );

    /**
     * Check for added admin menu's
     */
    public function __construct()
    {
        // Get loaded providers
        $providers = array_keys(\App::getLoadedProviders());

        // Get Tdt matches, but not core
        $packages = preg_grep('/^Tdt[\\\\](?!Core)/i', $providers);

        $menu = $this->core_menu;

        // Check for UI controller
        foreach ($packages as $package) {

            // Get package namespace
            $reflector = new \ReflectionClass($package);
            $namespace = $reflector->getNamespaceName();

            // Check for a UI controller
            $controller = $namespace . "\Ui\UiController";
            if (class_exists($controller)) {

                // Create controller instance
                $controller = \App::make($controller);

                $package_menu = @$controller->menu();

                // Check for added menu items
                if (!empty($package_menu)) {
                    $menu = array_merge($menu, $package_menu);
                }

                // Push for future use
                array_push($this->package_controllers, $controller);
            }
        }

        // Sort menu's
        usort($menu, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        // Share menu with views
        \View::share('menu', $menu);
    }

    /**
     * Handle other requests (packages)
     */
    public function handleRequest($uri)
    {
        // Check if package can do something with request
        $handled = false;

        // Check for UI controller
        foreach ($this->package_controllers as $controller) {

            $handled = $controller->handle($uri);

            // Break and return response if already handled
            if ($handled) {
                return $handled;
            }
        }

        // No candidates found
        if (!$handled) {
            return \App::abort(404, "Page not found.");
        }
    }
}

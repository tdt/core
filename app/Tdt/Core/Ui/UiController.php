<?php

/**
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;
use Config;
use App;
use Cookie;
use Tdt\Core\Repositories\Interfaces\DefinitionRepositoryInterface;

class UiController extends \Controller
{

    protected $package_controllers = array();

    protected $core_menu = array(
            array(
                'title' => 'General',
                'slug' => 'settings',
                'permission' => 'admin.dataset.view',
                'icon' => 'fa-cog',
                'priority' => 5
                ),
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
    public function __construct(DefinitionRepositoryInterface $definitions)
    {
        $this->definitions = $definitions;
        
        // Get loaded providers
        $providers = array_keys(\App::getLoadedProviders());

        // Get Tdt matches, but not core
        $packages = preg_grep('/^Tdt[\\\\](?!Core)/i', $providers);

        $menu = $this->core_menu;

        // UI translation
        $locale = Config::get('app.locale');
        $cookie_locale = Cookie::get('locale');
        // dd($cookie_locale);
        if ($cookie_locale && strlen($cookie_locale) == 2) {
            $locale = $cookie_locale;
        }
        App::setLocale($locale);

        // Check for UI controller
        foreach ($packages as $package) {
            // Get package namespace
            $reflector = new \ReflectionClass($package);
            $namespace = $reflector->getNamespaceName();

            $package = explode('\\', $namespace);
            $package = strtolower(array_pop($package));

            // Check for a UI controller
            $controller = $namespace . "\Ui\UiController";
            if (class_exists($controller)) {
                // Create controller instance
                $controller = \App::make($controller);

                $package_menu = @$controller->menu();

                $translated_menu = [];

                // Translate menu's
                foreach ($package_menu as $item) {
                    $title = trans($package . '::admin.menu_' . $item['slug']);

                    if (!empty($title)) {
                        $item['title'] = $title;
                    }

                    array_push($translated_menu, $item);
                }

                // Check for added menu items
                if (!empty($package_menu)) {
                    $menu = array_merge($menu, $translated_menu);
                }

                // Push for future use
                array_push($this->package_controllers, $controller);
            }
        }

        $translated_menu = [];

        // Sort menu's
        usort($menu, function ($a, $b) {
            return $a['priority'] - $b['priority'];
        });

        // Translate menu's
        foreach ($menu as $item) {
            $title = trans('admin.menu_' . $item['slug']);

            if (!empty($title) && $title != 'admin.menu_' . $item['slug']) {
                $item['title'] = $title;
            }

            array_push($translated_menu, $item);
        }

        // Share menu with views
        \View::share('menu', $translated_menu);
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

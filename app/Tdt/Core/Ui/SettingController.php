<?php

/**
 * The usercontroller
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Tdt\Core\Auth\Auth;
use \Language;
use Tdt\Core\Ui\Helpers\Flash;
use Tdt\Core\Repositories\SettingsRepository;

class SettingController extends UiController
{

    protected $settings;

    public function __construct(SettingsRepository $settings)
    {
        parent::__construct();

        $this->settings = $settings;
    }

    /**
     * Admin.settings.view
     */
    public function getIndex()
    {
        // Set permission
        Auth::requirePermissions('admin.dataset.view');

        // Get error
        $error = Flash::get();

        $languages = Language::all();

        // Fetch settings
        $settings = $this->settings->getAll();

        $view =  \View::make('ui.settings')
                    ->with('title', 'General settings | The Datatank')
                    ->with('languages', $languages)
                    ->with('settings', $settings)
                    ->with('error', $error);

        return \Response::make($view);
    }

    /**
     * Admin.settings.update
     */
    public function postIndex()
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.view');

        $settings_allowed = array(
                            'catalog_title',
                            'catalog_description',
                            'catalog_language',
                            'catalog_publisher_uri',
                            'catalog_publisher_name',
                            );

        $values = \Input::all();

        foreach ($values as $key => $value) {
            if (in_array($key, $settings_allowed)) {
                if ($key === 'catalog_publisher_uri') {
                    if (!filter_var($values['catalog_publisher_uri'], FILTER_VALIDATE_URL)) {
                        Flash::set('Publisher URI is not a valid URI.');
                        continue;
                    }
                }

                $this->settings->storeValue($key, $value);
            }
        }

        return \Redirect::to('api/admin/settings');
    }
}

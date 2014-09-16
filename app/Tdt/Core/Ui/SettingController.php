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
    public function postUpdate()
    {

        // Set permission
        Auth::requirePermissions('admin.dataset.view');

        try {
            if (empty($id)) {
                $id = \Input::get('id');
            }
            // Find the user using the user id
            $user = \Sentry::findUserById($id);

            // Update account
            if ($id > 2 && \Input::get('name')) {
                $user->email = strtolower(\Input::get('name'));
            }

            // Update password (not for the everyone account)
            if ($id > 1 && \Input::get('password')) {
                $resetCode = $user->getResetPasswordCode();
                $user->attemptResetPassword($resetCode, \Input::get('password'));
            }

            $user->save();

            // Find the group using the group id
            $group = \Sentry::findGroupById(\Input::get('group'));

            if ($id > 2) {
                // Remove user from previous groups
                foreach ($user->getGroups() as $g) {
                    $user->removeGroup($g);
                }

                // Assign the group to the user
                $user->addGroup($group);
            }

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            // Ignore and redirect back
        } catch (\Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
            // Ignore and redirect back
        }

        return \Redirect::to('api/admin/settings');
    }
}

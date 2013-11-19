<?php

namespace tdt\core\auth;
/**
 * Auth Controller
 * @copyright (C) 2011,2013 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
class Auth extends \Controller {

    /**
     * Check if user meets permissions required to do the request, otherwise prompt login
     */
    public static function requirePermissions($permissions = null){

        // Make sure permissions is an array
        if(!is_array($permissions)){
            $permissions = array($permissions);
        }

        // First check the permissions of the group 'everyone
        try{

            // Get the group
            $group = \Sentry::findGroupByName('everyone');

            // Get the group permissions
            $groupPermissions = $group->getPermissions();

            foreach($permissions as $permission){
                if(!empty($groupPermissions[$permission]) && $groupPermissions[$permission] == 1){
                    // Everyone has access
                    return true;
                }else{
                    break;
                }
            }

        }catch(\Cartalyst\Sentry\Groups\GroupNotFoundException $e){
            // Do nothing, proceed other checks
        }

        // Authenticate
        self::logIn();

        if(\Sentry::check()){

            // Get current user
            $user = \Sentry::getUser();

            // Check permissions
            if($user->hasAccess($permissions)){
                return true;
            }else{
                \App::abort(403, "The authenticated user hasn't got the permissions for this action.");
            }

        }else{
            \App::abort(401, 'Authentication is required.');
        }
    }

    /**
     * Log's user in
     */
    protected static function logIn(){

        header('WWW-Authenticate: Basic');
        header('HTTP/1.0 401 Unauthorized');

        // Fix basic auth on some servers;
        self::basicAuth();

        if(isset($_SERVER['PHP_AUTH_USER'])){
            try{
                // Set login credentials
                $credentials = array(
                    'email'    => $_SERVER['PHP_AUTH_USER'],
                    'password' => $_SERVER['PHP_AUTH_PW'],
                );

                // Try to authenticate the user
                $user = \Sentry::authenticate($credentials, false);

            }catch (\Cartalyst\Sentry\Users\LoginRequiredException $e){
                \App::abort(401, 'Authentication is required.');
            }catch (\Cartalyst\Sentry\Users\PasswordRequiredException $e){
                \App::abort(401, 'Authentication is required.');
            }catch (\Cartalyst\Sentry\Users\WrongPasswordException $e){
                \App::abort(401, 'Authentication is required, username and password mismatch.');
            }catch (\Cartalyst\Sentry\Users\UserNotFoundException $e){
                \App::abort(401, 'Authentication is required, username and password mismatch.');
            }catch (\Cartalyst\Sentry\Users\UserNotActivatedException $e){
                \App::abort(403, 'Authentication is required, user is not activated.');
            }

            // The following is only required if throttle is enabled
            catch (\Cartalyst\Sentry\Throttling\UserSuspendedException $e){
                \App::abort(403, 'Authentication is required, user is suspended.');
            }catch (\Cartalyst\Sentry\Throttling\UserBannedException $e){
                \App::abort(403, 'Authentication is required, user is banned.');
            }
        }else{
            \App::abort(401, 'Authentication is required.');
        }
    }

    /**
     *  Fix for empty PHP_AUTH_USER
     */
    protected static function basicAuth(){
        //
        if(!empty($_SERVER['Authorization'])){
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['Authorization'], 6)));
        }
    }


}
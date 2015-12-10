<?php

/**
 * @copyright (C) 2011, 2014 by OKFN Belgium vzw/asbl
 * @license AGPLv3
 * @author Michiel Vancoillie <michiel@okfn.be>
 */
namespace Tdt\Core\Ui;

use Cookie;
use Redirect;

class LanguageController extends \Controller
{

    /**
     * Change the UI language
     */
    public function setLanguage($lang)
    {
        if ($lang && strlen($lang) == 2) {
            Cookie::queue('locale', $lang, 0);
        }

        return Redirect::back();
    }
}

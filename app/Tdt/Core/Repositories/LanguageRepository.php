<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface;

class LanguageRepository implements LanguageRepositoryInterface
{


    public function getById($language_id)
    {

        $lang = \Language::where('lang_id', '=', $language_id)->first();

        if(!empty($lang))
            return $lang->toArray();

        return $lang;
    }

    public function getAll()
    {
        return \Language::all(array('lang_id','lang_code','name'))->toArray();
    }
}

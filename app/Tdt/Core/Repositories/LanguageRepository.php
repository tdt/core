<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\LanguageRepositoryInterface;

class LanguageRepository implements LanguageRepositoryInterface
{

    public function getByCode($language_code)
    {

        $lang = \Language::where('lang_code', '=', $language_code)->first();

        if (!empty($lang)) {
            return $lang->toArray();
        }

        return $lang;
    }

    public function getAll()
    {
        return \Language::all(array('lang_id','lang_code','name'))->toArray();
    }

    /**
     * Get a language by its given name
     *
     * @param string $name
     *
     * @return array Language
     */
    public function getByName($name)
    {
        $lang = \Language::where('name', '=', $name)->first();

        if (!empty($lang)) {
            return $lang->toArray();
        }

        return $lang;
    }
}

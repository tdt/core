<?php

namespace Tdt\Core\Repositories\Interfaces;

interface LanguageRepositoryInterface
{

    /**
     * Fetch a Language by code
     *
     * @param integer $code
     * @return array Language
     */
    public function getByCode($language_code);

    /**
     * Fetch all languages
     *
     * @return array of Language's
     */
    public function getAll();

    /**
     * Fetch a Language by name
     *
     * @param integer $name
     * @return array Language
     */
    public function getByName($name);
}

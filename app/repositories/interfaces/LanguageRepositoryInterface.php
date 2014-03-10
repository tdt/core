<?php

namespace repositories\interfaces;

interface LanguageRepositoryInterface
{

    /**
     * Fetch a Language by id
     *
     * @param integer $id
     * @return array Language
     */
    function getById($language_id);

    /**
     * Fetch all languages
     *
     * @return array of Language's
     */
    function getAll();
}

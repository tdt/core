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
}

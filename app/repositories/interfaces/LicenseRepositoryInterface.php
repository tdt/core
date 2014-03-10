<?php

namespace repositories\interfaces;

interface LicenseRepositoryInterface
{

    /**
     * Fetch a License by title
     *
     * @param string $title
     * @return array License
     */
    function getByTitle($title);

    /**
     * Fetch all licenses
     *
     * @return array of License's
     */
    function getAll();
}

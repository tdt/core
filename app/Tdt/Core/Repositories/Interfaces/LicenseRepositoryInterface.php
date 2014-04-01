<?php

namespace Tdt\Core\Repositories\Interfaces;

interface LicenseRepositoryInterface
{

    /**
     * Fetch a License by title
     *
     * @param string $title
     * @return array License
     */
    public function getByTitle($title);

    /**
     * Fetch all licenses
     *
     * @return array of License's
     */
    public function getAll();
}

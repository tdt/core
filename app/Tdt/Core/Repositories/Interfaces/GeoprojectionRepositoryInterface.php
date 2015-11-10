<?php

namespace Tdt\Core\Repositories\Interfaces;

interface GeoprojectionRepositoryInterface
{

    /**
     * Fetch a Geoprojection by code
     *
     * @param string $code
     * @return array
     */
    public function getByCode($epsg_code);

    /**
     * Fetch all geoprojections
     *
     * @return array of Geoprojection's
     */
    public function getAll();
}

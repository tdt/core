<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\LicenseRepositoryInterface;

class LicenseRepository implements LicenseRepositoryInterface
{
    /**
     * Fetch a License by its title (=id)
     */
    public function getByTitle($title)
    {
        $license = \License::where('title', '=', $title)->first();

        if (!empty($license)) {
            return $license->toArray();
        }

        return $license;
    }

    public function getAll()
    {
        return \License::all(
            array(
                'license_id',
                'title',
                'url'
            )
        )->toArray();
    }
}

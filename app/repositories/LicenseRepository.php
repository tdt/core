<?php

namespace repositories;

use repositories\interfaces\LicenseRepositoryInterface;

class LicenseRepository implements LicenseRepositoryInterface
{

    /**
     * Fetch a License by its title (=id)
     */
    public function getByTitle($title)
    {

        $license = \License::where('title', '=', $title)->first();

        if(!empty($license))
            return $license->toArray();

        return $license;
    }

    public function getAll()
    {
        return \License::all(array(
                            'domain_content',
                            'domain_data',
                            'domain_software',
                            'family',
                            'license_id',
                            'is_generic',
                            'is_okd_compliant',
                            'is_osi_compliant',
                            'maintainer',
                            'status',
                            'title',
                            'url'
                        ))->toArray();
    }
}
<?php

namespace Tdt\Core\Repositories;

use Tdt\Core\Repositories\Interfaces\GeoprojectionRepositoryInterface;

class GeoprojectionRepository implements GeoprojectionRepositoryInterface
{
    public function getByCode($epsg_code)
    {
        $lang = \Geoprojection::where('epsg', '=', $epsg_code)->first();

        if (!empty($lang)) {
            return $lang->toArray();
        }

        return $lang;
    }

    public function getAll()
    {
        return \Geoprojection::all(array('epsg','projection'))->toArray();
    }
}

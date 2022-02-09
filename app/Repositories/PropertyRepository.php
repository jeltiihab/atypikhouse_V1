<?php

namespace App\Repositories;

use App\Models\Property;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
//use Your Model

/**
 * Class PropertyRepository.
 */
class PropertyRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model(): string
    {
        return Property::class;
    }


    public function findPropertiesBySearchCity($hosting_capacity = null, $city = null)
    {
        return $this->model->joinRelationship('address', function ($join) use ($city) {
            $join->where('addresses.city', $city);
        })
            ->where('hosting_capacity','>=',$hosting_capacity)
            ->where('is_activated',true)
            ->get();


        //return $this->model->address->fil
    }
}

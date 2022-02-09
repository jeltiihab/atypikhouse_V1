<?php

namespace App\Repositories;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;

//use Your Model

/**
 * Class ReservationRepository.
 */
class ReservationRepository extends BaseRepository
{
    /**
     * @return string
     *  Return the model
     */
    public function model(): string
    {
        return Reservation::class;
    }


    public function findReservationByPropretyId($propertyId, $arrivalDate): array|\Illuminate\Database\Eloquent\Collection
    {

        return $this->newQuery()
            ->where('departure_date', $arrivalDate, '>=')
            ->where('property_id', $propertyId, '=')
            ->orderBy('arrival_date')
            ->get();

    }

    public function findReservationByArrival($arrivalDate, $departureDate, $propertyId): \Illuminate\Database\Eloquent\Collection|array
    {

        return $this->model->newQuery()
            ->whereBetween('arrival_date', [$arrivalDate, $departureDate])
            ->where('property_id', "=", $propertyId)
            ->get();
    }

    public function findReservationByDeparture($arrivalDate, $departureDate, $propertyId): \Illuminate\Database\Eloquent\Collection|array
    {
        return $this->model->newQuery()
            ->whereBetween('departure_date', [$arrivalDate, $departureDate])
            ->where('property_id', "=", $propertyId)
            ->get();
    }

    public function findReservationbyAll($arrivalDate, $departureDate, $propertyId): array|\Illuminate\Database\Eloquent\Collection
    {

        return DB::select("SELECT *  FROM reservations as r
                                HAVING DATEDIFF(:arrivalDate,r.arrival_date) = 0
                                and DATEDIFF(:departureDate,r.departure_date) = 0
                                and r.property_id = :propertyId and r.status = 'payed' ", [
                                    "arrivalDate" => $arrivalDate,
                                    "departureDate" => $departureDate,
                                    "propertyId" => $propertyId
                                ]);


    }


}

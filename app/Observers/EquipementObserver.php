<?php

namespace App\Observers;

use App\Models\Equipement;

class EquipementObserver
{
    /**
     * Handle the Equipement "created" event.
     *
     * @param  \App\Models\Equipement  $equipement
     * @return void
     */
    public function created(Equipement $equipement)
    {
        //
    }

    /**
     * Handle the Equipement "updated" event.
     *
     * @param  \App\Models\Equipement  $equipement
     * @return void
     */
    public function updated(Equipement $equipement)
    {
        //
    }

    /**
     * Handle the Equipement "deleted" event.
     *
     * @param  \App\Models\Equipement  $equipement
     * @return void
     */
    public function deleted(Equipement $equipement)
    {
        //
    }

    /**
     * Handle the Equipement "restored" event.
     *
     * @param  \App\Models\Equipement  $equipement
     * @return void
     */
    public function restored(Equipement $equipement)
    {
        //
    }

    /**
     * Handle the Equipement "force deleted" event.
     *
     * @param  \App\Models\Equipement  $equipement
     * @return void
     */
    public function forceDeleted(Equipement $equipement)
    {
        //
    }
}

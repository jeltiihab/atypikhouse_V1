<?php

namespace App\Observers;

use App\Mail\AttributeAdded;
use App\Mail\AttributeModified;
use App\Models\Attribute;
use App\Notifications\AttributeNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AttributeObserver
{
    /**
     * Handle the Attribute "created" event.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return void
     */
    public function created(Attribute $attribute)
    {
        $attribute->Category?->properties->map(function ( $item, $key) use ($attribute) {
           $item->user->notify(new AttributeNotification(new AttributeAdded($attribute))) ;
           return $item ;
       }) ;


    }

    /**
     * Handle the Attribute "updated" event.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return void
     */
    public function updated(Attribute $attribute)
    {
        $attribute->Category?->properties->map(function ( $item, $key) use ($attribute) {
            //dd($item->dynamic_attributes) ;
            $arr = $item->dynamic_attributes ;
            $oldKey = Str::replace(" ","_",$attribute->getOriginal('name') ) ;
            $arr[$attribute->name] = $arr[$oldKey] ;
            unset($arr[$oldKey]) ;
            $item->dynamic_attributes = $arr ;
            $item->saveQuietly() ;
            $item->user->notify(new AttributeNotification(new AttributeModified($attribute))) ;
            return $item ;
        }) ;



    }

    /**
     * Handle the Attribute "deleted" event.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return void
     */
    public function deleted(Attribute $attribute)
    {

    }

    /**
     * Handle the Attribute "restored" event.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return void
     */
    public function restored(Attribute $attribute)
    {
        //
    }

    /**
     * Handle the Attribute "force deleted" event.
     *
     * @param  \App\Models\Attribute  $attribute
     * @return void
     */
    public function forceDeleted(Attribute $attribute)
    {
        //
    }
}

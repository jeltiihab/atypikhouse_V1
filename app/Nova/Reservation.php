<?php

namespace App\Nova;

use Illuminate\Http\Request;
use KirschbaumDevelopment\Nova\InlineSelect;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Timothyasp\Badge\Badge;


class Reservation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Reservation::class;

    public static $group = "Gestion des biens";

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'ref';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'ref', 'user_id', 'property_id', 'arrival_date', 'departure_date', 'hosting_capacity', 'price', 'status', 'id'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Reservations');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Reservation');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('Id'), 'id')
                ->rules('required')
                ->sortable()
            ,
            BelongsTo::make('User')
                ->searchable(false)->withoutTrashed()->updateRules('sometimes')
                ->sortable()
            ,
            BelongsTo::make('property')
                ->searchable(false)->withoutTrashed()->updateRules('sometimes')
                ->sortable()
            ,
            DateTime::make(__(  "Arrivée"), "arrival_date")
                ->rules('required')->updateRules('sometimes')
                ->sortable()
            ,
            DateTime::make(__('Départ'), 'departure_date')->updateRules('sometimes')
                ->rules('required')
                ->sortable()
            ,
            Badge::make(__('Reference'), 'ref')
                ->options([$this->ref=>$this->ref])
                ->colors([$this->ref=> "#7E3BEF"])
                ->readonly()
                ->sortable()
            ,
            Number::make(__('Pieces'), 'hosting_capacity')
                ->sortable()
            ,
            Text::make(__('Price'), 'Prix')->updateRules('sometimes')
                ->rules('required')
                ->sortable()
            ,
            InlineSelect::make(__('Status'), 'status')
                ->options([
                    "payed"=>"payed",
                    "waiting"=>"waiting"
                ])
                ->rules('required')
                ->inlineOnIndex()
                ->disableTwoStepOnIndex()

                ->sortable()
            ,
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function actions(Request $request)
    {
        return [];
    }
}

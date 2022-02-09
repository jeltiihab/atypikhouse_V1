<?php

namespace App\Nova;

use Enmaboya\CountrySelect\CountrySelect;
use Enmaboya\PlaceInput\PlaceInput;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;


class Address extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Address::class;

    public static $group = "Gestion des biens";

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public function title()
    {
        return $this->postal_code . ' ' . $this->city;
    }

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'id', 'street', 'street_number', 'postal_code', 'city', 'country', 'region'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Addresses');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Address');
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
            Text::make(__('Rue'), 'street')
                ->sortable()
            ,
            Number::make(__('Numero ru rue'), 'street_number')
                ->sortable()
            ,
            Text::make(__('Code postal '), 'postal_code')
                ->sortable()
            ,

            CountrySelect::make(__('Pays'), 'country')
                ->only(['FR'])
                ->sortable()
            ,
            Text::make(__('Ville'), 'city')
                ->sortable()
            ,

            Text::make(__('Region'), 'region')
                ->sortable()
            ,

            BelongsTo::make('property')
            ->searchable(false)
            ->withoutTrashed()
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

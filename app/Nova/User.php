<?php

namespace App\Nova;

use Davidpiesse\NovaToggle\Toggle;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Password;
use Naif\GeneratePassword\GeneratePassword;


class User extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\User::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'id', 'name', 'email', 'firstName', 'sexe', 'lastName', 'birthDate', 'phone', 'is_activated'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Utilisateurs');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('User');
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

            Gravatar::make()->maxWidth(50),


            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            GeneratePassword::make("Mot de pass",'Password')
                ->onlyOnForms()
                ->creationRules('required', 'string', 'min:8')
                ->length(12)
                ->updateRules('nullable', 'string', 'min:8'),

            Select::make(__('Sexe'), 'sexe')
                ->sortable()
                ->hideFromIndex()
                ->options([
                    'homme' => 'homme',
                    'femme' => 'femme',
                ])
            ,
            Text::make(__('Prénom'), 'firstName')
                ->sortable()
            ,
            Text::make(__('Nom'), 'lastName')
                ->sortable()
            ,
            DateTime::make(__('Naissance'), 'birthDate')
                ->hideFromIndex()
                ->sortable()
            ,
            Text::make(__('tel'), 'phone')
                ->hideFromIndex()
                ->sortable()
            ,
            Toggle::make(__('Is Activated'), 'is_activated')
                ->rules('required')
                ->editableIndex()
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

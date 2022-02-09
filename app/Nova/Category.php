<?php

namespace App\Nova;

use Benjacho\BelongsToManyField\BelongsToManyField;
use DigitalCreative\ResourceNavigationTab\HasResourceNavigationTabTrait;
use DigitalCreative\ResourceNavigationTab\ResourceNavigationTab;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Zareismail\NovaWizard\Step;


class Category extends Resource
{
    use HasResourceNavigationTabTrait;
    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Category::class;

    public static $group = "Equipements";

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'id', 'name'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Categories');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Category');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return  array
     */
    public function fields(Request $request): array
    {

        return [
            ResourceNavigationTab::make([
                'label' => 'Information1',
                'fields' => [

                    ID::make(__('Id'), 'id')
                        ->sortable()
                    ,
                    Text::make(__('Nom'), 'name')
                        ->rules('required')
                        ->sortable()
                    ,

                ]
            ]),

            ResourceNavigationTab::make([
                'label' => 'Equipments',
                'fields' => [
                    BelongsToMany::make('equipements')->withSubtitles(),
                    BelongsToManyField::make('equipements')->canSelectAll()->showAsListInDetail()->hideFromDetail()

                ]
            ]),

            ResourceNavigationTab::make([
                'label' => 'Dynamic Attributes',
                'fields' => [

                    HasMany::make('attributes')



                ]
            ]),












        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return  array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param Request $request
     * @return  array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param Request $request
     * @return  array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param Request $request
     * @return  array
     */
    public function actions(Request $request)
    {
        return [];
    }
}

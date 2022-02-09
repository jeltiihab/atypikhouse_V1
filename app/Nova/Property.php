<?php

namespace App\Nova;

use Armincms\Json\Json;
use Davidpiesse\NovaToggle\Toggle;
use DigitalCreative\JsonWrapper\JsonWrapper;
use Illuminate\Http\Request;
use KirschbaumDevelopment\NovaComments\Commenter;
use KirschbaumDevelopment\NovaComments\CommentsPanel;
use Laraning\NovaTimeField\TimeField;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;

use Laravel\Nova\Tool;
use Michielfb\Time\Time;
use Nikaia\Rating\Rating;
use Vyuldashev\NovaMoneyField\Money;
use Yassi\NestedForm\NestedForm;
use Zareismail\NovaWizard\Contracts\Wizard;
use Zareismail\NovaWizard\Step;

use DigitalCreative\ResourceNavigationTab\HasResourceNavigationTabTrait;
use DigitalCreative\ResourceNavigationTab\ResourceNavigationTab;


class Property extends Resource implements Wizard
{
    use HasResourceNavigationTabTrait;

    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \App\Models\Property::class;

    public static $group = "Gestion des biens";

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
        'id', 'name', 'user_id', 'category_id', 'address_id', 'rooms', 'description', 'surface', 'location', 'hosting_capacity', 'check_in_at', 'check_out_at', 'rate', 'reviews', 'price', 'is_activated'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Biens');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Property');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return  array
     */
    public function fields(Request $request): array
    {
        $attributes = $this->category?->attributes ;
        $equipements = $this->category?->equipements ;

        $attributesFields = [];
        $equipementsFields = [] ;
        if(!is_null($attributes))
            if($attributes?->isNotEmpty()) {
                $attributesFields = $attributes->map(function ($item,$key){
                    $field = null;
                    switch ($item->type){
                        case "text":
                            $field = Text::make($item->name)->rules( $item->is_required?"required":"")->hideFromIndex();
                            break ;
                        case "boolean":
                            $field = Toggle::make($item->name)->rules( $item->is_required?"required":"")->hideFromIndex();
                            break ;
                        case "numeric":
                            $field = Number::make($item->name)->rules( $item->is_required?"required":"")->hideFromIndex();
                            break ;
                    }
                    return $field ;
                })->all();

            }

        if(!is_null($equipements))
            if($equipements?->isNotEmpty()) {
                $equipementsFields = $equipements->map(function ($item,$key) {

                    return Toggle::make($item->name)->hideFromIndex() ;

                })->all();
            }







       return [
           ResourceNavigationTab::make([
            'label' => 'Info',
            'fields' => [
                (new Step(__('Information génerales'), [


                    ID::make(__('Id'), 'id')
                        ->sortable()
                    ,
                    Text::make(__('Titre'), 'name')
                        ->sortable()
                        ->hideFromIndex()
                    ,
                    BelongsTo::make('User')
                        ->searchable(false)
                        ->withoutTrashed()
                        ->sortable()
                    ,
                    BelongsTo::make('Category')
                        ->searchable(false)

                        ->withoutTrashed()
                        ->sortable()

                    ,
                    HasOne::make("address")


                ]))->withToolbar()


            ]
        ]),

           ResourceNavigationTab::make([
               'label' => 'Info complémentaire',
               'fields' => [
                   (new Step(__('Information complémentaire'), [
                       Number::make(__('Rooms'), 'rooms')
                           ->sortable()
                           ->min(1)
                           ->step(1)
                       ,
                       Textarea::make(__('Description'), 'description')
                           ->sortable()
                           ->hideFromIndex()
                       ,
                       Number::make(__('Nombre des pieces'), 'hosting_capacity')
                           ->sortable()
                           ->min(1)
                           ->step(1)


                   ]))->withToolbar()


               ]
           ]),

           ResourceNavigationTab::make([
               'label' => 'horaires et prix',
               'fields' => [
                   (new Step(__('Informations horaires et prix'), [
                       TimeField::make(__('Check In At'), 'check_in_at')
                           ->sortable()
                           ->hideFromIndex()
                       ,
                       TimeField::make(__('Check Out At'), 'check_out_at')
                           ->sortable()
                           ->hideFromIndex()
                       ,
                       Rating::make( 'rate')
                           ->min(0)->max(5)->increment(0.5)

                       ,
                       Money::make(__('Prix'), 'EUR','price')

                           ->sortable()
                       ,
                       Toggle::make(__('Is Activated'), 'is_activated')
                           ->trueColor('#21b978')
                           ->falseColor('#e74444')
                           ->editableIndex(),

                   ]))->withToolbar()


               ]
           ]),

           ResourceNavigationTab::make([
               'label' => 'Propriétes dynamiques',
               'fields' => is_null($attributesFields) ?  []: [
                   (new Step(__('Propriétes dynamiques'), [

                           Json::make('dynamic_attributes', $attributesFields)
                       ]
                   ))->withToolbar()


               ]
           ]),

           ResourceNavigationTab::make([
               'label' => 'Equipements',
               'fields' => empty($equipementsFields) ? []: [
                   (new Step(__('Step 5'), [

                           Json::make('equipments', $equipementsFields) ,
                       ]
                   ))->withToolbar()


               ]
           ]),

           ResourceNavigationTab::make([
               'label' => 'Commentaires',
               'fields' => [
                   new Commenter(),
                   new CommentsPanel(),
               ]
           ]),

       ] ;



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

<?php

namespace App\Providers;

use App\Models\Reservation;
use App\Nova\Metrics\CountIcome;
use App\Nova\Metrics\CountProps;
use App\Nova\Metrics\CountUsers;
use Cloudstudio\ResourceGenerator\ResourceGenerator;
use Coroowicaksono\ChartJsIntegration\AreaChart;
use Coroowicaksono\ChartJsIntegration\PieChart;
use Coroowicaksono\ChartJsIntegration\StackedChart;
use DigitalCreative\NovaDashboard\Examples\ExampleDashboard;
use DigitalCreative\NovaDashboard\NovaDashboard;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use NovaBi\NovaDashboardManager\DashboardManager;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    protected function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }

    /**
     * Get the cards that should be displayed on the default Nova dashboard.
     *
     * @return array
     */
    protected function cards()
    {
        return [
            new CountUsers(),
            new CountProps(),
            new CountIcome(),
            (new PieChart())
                ->title('Reservation')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->model(Reservation::class) // Use Your Model Here
                ->series([
                    [
                        'label' => 'Payed Reservation',
                        'filter' => [
                            'key' => 'status', // State Column for Count Calculation Here
                            'value' => 'payed'
                        ],
                    ],
                    [
                    'label' => 'non Payer',
                    'filter' => [
                        'key' => 'status', // State Column for Count Calculation Here
                        'value' => 'waiting'
                    ],
                ]
                ])
                ->width('1/3'),
            (new StackedChart())
                ->title('Reservation')
                ->animations([
                    'enabled' => true,
                    'easing' => 'easeinout',
                ])
                ->model(Reservation::class) // Use Your Model Here
                ->series([
                    [
                        'label' => 'Payed Reservation',
                        'filter' => [
                            'key' => 'status', // State Column for Count Calculation Here
                            'value' => 'payed'
                        ],
                    ],
                ])
                ->width('2/3'),
        ];
    }

    /**
     * Get the extra dashboards that should be displayed on the Nova dashboard.
     *
     * @return array
     */
    protected function dashboards()
    {

        //dd($mydash) ;
        return [];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {


        return [

            new ResourceGenerator(),
            new \NovaTrust\NovaTrust,


        ];
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

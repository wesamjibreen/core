<?php
/**
 * Created by PhpStorm.
 * User: WeSSaM
 * Date: 11/4/2022
 * Time: 4:03 م
 */

namespace Core;

use Carbon\Carbon;
use Core\Interfaces\RepositoryInterface;
use Core\Repositories\BaseRepository;
use Core\Services\ImageService;
use Core\Traits\Provider\Exception;
use Core\Traits\Provider\Relations;
use Core\Traits\Provider\Routing as RoutingMacro;
use Core\Traits\Provider\Response as ResponseMacro;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Messaging\MessagingServiceProvider;


class CoreServiceProvider extends ServiceProvider
{
    use RoutingMacro, ResponseMacro, Exception , Relations;

    /**
     * list of package's helpers
     * @author WeSSaM
     * @var array
     */
    protected $helpers = [
        'Constants',
        'CommonFunctions'
    ];

    /**
     * Register a service provider with the application.
     *
     * @return void
     * @author WeSSaM
     */
    public function register()
    {
        $this->bindingCustomException();
        $this->registerHelpers();
        $this->app->register(MessagingServiceProvider::class);
//        $this->app->extend(BaseRepository::class, function ($repository, $app) {
//
//            dd($repository);
////            return new DecoratedService($repository);
//        });

//        $this->app->when(RepositoryInterface::class)
//            ->needs('$imageManager')->give(function () {
//                return new ImageService;
//            });

//        $this->app->bind(RepositoryInterface::class, function () {
//            dd('RepositoryInterface');
//            return new BaseRepository(new ImageService);
//        });
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * set default string length for all migration files @author WeSSaM
         */
        Schema::defaultStringLength(191);

        $this->publishes([
            __DIR__ . '/../config/core.php' => config_path('core.php'),
        ]);

        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        $this->loadRoutesFrom(__DIR__ . '/Routes/api.php');

        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');

        $this->loadTranslationsFrom(__DIR__ . '/Resources/Lang', 'Core');

        $this->macros();
    }


    /**
     * register package's helpers
     * @author WeSSaM
     */
    function registerHelpers()
    {
        foreach ($this->helpers as $helper) {
            $helper_path = __DIR__ . '/Helpers/' . $helper . '.php';
            if (File::isFile($helper_path))
                require_once $helper_path;
        }
    }


    /**
     * register all macros
     * @author WeSSaM
     */
    function macros()
    {
        /**
         * injecting resource macro to generate custom restfull CRUD  @author WeSSaM
         */
        $this->resourceRoutes();

        /**
         * injecting generated auth routes according to particular module   @author WeSSaM
         */
        $this->authApiRoutes();

        /**
         * injecting uploading route's function @author WeSSaM
         */
        $this->uploadingRoutes();

        /**
         * call responseMacro Macro  @author WeSSaM
         */
        $this->responseMacro();


        $this->relationsMacro();
    }



}

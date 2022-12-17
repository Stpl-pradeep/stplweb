<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Service;
use App\Models\SettingsModel;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException; //Import exception.



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        Paginator::useBootstrap();
        //  Paginator::defaultView('default');

        //  Paginator::defaultSimpleView('ecommerce-pagination');

        View::composer(['layouts.navigation', 'layouts.footer', 'layouts.app','layouts.admin','admin.login'], function ($view) {

            // Start Settings Query
            $Settings = SettingsModel::where(['status' => 0])->first();
            $Category = Category::where(['status' => 0, 'del'=>0])->get();
            $Service = Service::where(['status' => 0, 'del'=>0])->get();
            // End Settings Query 

            // Start Header Menu
            $UrlVal = url()->current();
            $Url = explode('/', $UrlVal);

            if (isset($Url[3]) and $Url[3] == 'page') {
                $Page = Page::where(['slug' => $Url[4], 'del' => 0, 'status' => 0])->firstOrFail();
            }

            $MenuItem = Menu::where(['layout_type' => 0, 'parent_id' => 0, 'del' => 0, 'status' => 0])->orderBy('order_by')->get();
            $i = 0;
            $MemuList = array();
            foreach ($MenuItem as $key => $value) {
                $MemuList[$i] = $value;
                if ($count = Menu::where(['layout_type' => 0, 'parent_id' => $value->id, 'del' => 0, 'status' => 0])->orderBy('order_by')->count()) {
                    $MenuItem = Menu::where(['layout_type' => 0, 'parent_id' => $value->id, 'del' => 0, 'status' => 0])->orderBy('order_by')->get();
                    $MemuList[$i]['submenu'] = $MenuItem;
                }
                $i++;
            }

            // End Header Menu

            // Start Footer Menu
            $FooterItems = Menu::where(['layout_type' => 1, 'parent_id' => 0, 'del' => 0, 'status' => 0])->orderBy('order_by')->get();
            $i = 0;
            $FooterList = array();
            foreach ($FooterItems as $key => $value) {
                $FooterList[$i] = $value;
                if ($count = Menu::where(['layout_type' => 1, 'parent_id' => $value->id, 'del' => 0, 'status' => 0])->orderBy('order_by')->count()) {

                    $FooterItems = Menu::where(['layout_type' => 1, 'parent_id' => $value->id, 'del' => 0, 'status' => 0])->orderBy('order_by')->get();
                    $FooterList[$i]['submenu'] = $FooterItems;
                }
                $i++;
            }
            // End Footer Menu

           $pagesList = Page::where(['del'=>0, 'status'=>0])->get();
            $view->with(compact('pagesList','Service','Settings', 'MemuList', 'MenuItem', 'FooterItems', 'FooterList', 'Category'));
        });
    }
}

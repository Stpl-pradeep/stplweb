<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Requests\MenuRequest;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class MenusController extends Controller
{
   /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function index()
   {
      $Title = "Menu List";
      $MenuList = Menu::where(['del' => 0])->orderBy('id', 'DESC')->paginate(10);
      $PageList = Page::where('del', 0)->orderBy('id', 'DESC')->get();
      return view('admin.menus.menulist', compact('MenuList', 'Title', 'PageList'));
   }

   /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function create()
   {
      $Title = "Add New Menus";
      $PageList = Page::where('del', 0)->orderBy('id', 'DESC')->get();
      $MenuList = Menu::where(['del' => 0, 'parent_id' => 0])->orderBy('id', 'DESC')->get();
      return view('admin.menus.createnewmenus', compact('Title', 'PageList', 'MenuList'));
   }

   /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
   public function store(MenuRequest $request)
   {
      if (!empty($request->input('layout_type'))) {
         $LayoutType = $request->input('layout_type');
      } else {
         $LayoutType = 0;
      }

      if (!empty($request->input('name'))) {
         $name = $request->input('name');
      } else {
         $name = null;
      }

      if (!empty($request->input('menu_type'))) {
         $MenuType = $request->input('menu_type');
      } else {
         $MenuType = null;
      }

      if (!empty($request->input('order_by'))) {
         $OrderBy = $request->input('order_by');
      } else {
         $OrderBy = null;
      }

      if (!empty($request->input('parent_id'))) {
         $ParentId = $request->input('parent_id');
      } else {
         $ParentId = 0;
      }

      if (!empty($request->input('url'))) {
         $Url = $request->input('url');
      } else {
         $Url = null;
      }

      if (!empty($request->input('type'))) {
         $Type = $request->input('type');
      } else {
         $Type = null;
      }

      if (!empty($request->input('page_id'))) {
         $PageId = $request->input('page_id');
      } else {
         $PageId = null;
      }

      Menu::create([
         'name' => $name,
         'layout_type' => $LayoutType,
         'menu_type' => $MenuType,
         'order_by' => $OrderBy,
         'parent_id' => $ParentId,
         'url' => $Url,
         'type' => $Type,
         'page_id' => $PageId,
         'created_at' => Carbon::now(),
      ]);

      return redirect('admin/menus/create')->with('msg', 'Your menu has been added successfully!');
   }

   /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function show(Request $request,$id)
   {
      $Title = "Menu Detail";
        $EditSlug = Menu::where(['id' => $id])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $MenuDetail = Menu::where('id', $id)->first();
        return view('admin.menus.menusdetail', compact('MenuDetail', 'Title'));
   }

   /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function edit(Request $request, $id)
   {
      $EditSlug = Menu::where(['del' => 0, 'id' => $id])->first();
      if (!$EditSlug) {
         $request->session()->flash('errorMsg', 'Record Not Found');
         return redirect()->back();
      }
      $EditMenus = Menu::where('id', $id)->first();

      $Title = "Edit Menus";
      $PageList = Page::where('del', 0)->orderBy('id', 'DESC')->get();
      $MenuList = Menu::where(['del' => 0, 'parent_id' => 0])->orderBy('id', 'DESC')->get();

      return view('admin.menus.editmenus', compact('Title', 'PageList', 'MenuList', 'EditMenus'));
   }

   /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function update(Request $request, $id)
   {
      Menu::where('id', $id)->update([
         'name' => $request->input('name'),
         'layout_type' => $request->input('layout_type'),
         'menu_type' => $request->input('menu_type'),
         'order_by' => $request->input('order_by'),
         'parent_id' => $request->input('parent_id'),
         'url' => $request->input('url'),
         'type' => $request->input('type'),
         'page_id' => $request->input('page_id'),
      ]);
      return redirect('admin/menus')->with('msg', 'Your menus has been update successfully!');
   }

   /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */
   public function destroy(Request $request, $id)
   {
      $delSlug = Menu::where(['del' => 0, 'id' => $id])->first();
      if (!$delSlug) {
         $request->session()->flash('errorMsg', 'Record Not Found');
         return redirect()->back();
      }
      $menu = Menu::where('id', $id)->update(['del' => 1]);
      return redirect('admin/menus')->with('errormsg', 'Your menu has been deleted successfully!');
   }

   public function menustatus(Request $request)
   {
      $data = $request->all();
      Menu::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
   }

   public function SearchMenuist(Request $request)
   {
      $data = $request->all();
      if ($request->ismethod('get')) {

         if (!empty($data['name'])) {
            $name = $data['name'];
         }

         if (!empty($data['menu_type'])) {
            $menu_type = $data['menu_type'];
         }
         if (!empty($data['page_id'])) {
            $page_id = $data['page_id'];
         }

         $MenuList = Menu::query();
         if (!empty($name)) {
            $MenuList = $MenuList->where('name', 'LIKE', "%{$name}%");
         }
         if (!empty($menu_type)) {
            $MenuList = $MenuList->where('menu_type', $menu_type);
         }

         if (!empty($page_id)) {
            $MenuList = $MenuList->where('page_id', $page_id);
         }
         $MenuList = $MenuList->where('del',0)->paginate(10);
         $MenuList->appends($request->all());
         $Title = "Menu List";
         $PageList = Page::where('del', 0)->orderBy('id', 'DESC')->get();
         return view('admin.menus.menulist', compact('MenuList', 'Title', 'PageList'));
      }
   }
}

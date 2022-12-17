<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Gallery;
use Illuminate\Http\Request;
use App\Models\Page;

class PageController extends Controller
{
  public function Pages($slug)
  {
    $Title = "Pages";
    $Page = Page::where('slug', $slug)->firstOrFail();
    return view('users.pages.pages', compact('Page','Title'));
  }

  public function galleryPage(){
    $Title = "Gallery";
    $galleryList = Gallery::where('del',0)->orderBy('id','desc')->paginate(20);
    return view('users.pages.gallerypage', compact('galleryList', 'Title'));
  }

  public function documentPage()
  {
    $Title = "Documents";
    $documentList = Document::where('del', 0)->orderBy('id', 'desc')->paginate(20);
    return view('users.pages.documentpage', compact('documentList', 'Title'));
  }

}

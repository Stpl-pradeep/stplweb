<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function supportContactList()
    {
        $Title = "Support List";
        $supportList = Contact::orderBy('id','DESC')->paginate(10);
        return view('admin.users.supportlist', compact('supportList', 'Title'));
    }

}

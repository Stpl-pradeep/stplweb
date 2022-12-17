<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Geolocation;
use App\Models\Order;
use App\Models\OrdersDetail;
use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash as FacadesHash;
use Illuminate\Support\Facades\Session as FacadesSession;

class UsersAuthController extends Controller
{

    public function userAccount(Request $request)
    {
        $Title = "My Account";
        $userId = Auth::user()->id;
        $shippingAddress = ShippingAddress::where(['user_id' => $userId])->first();
        $ordersList = Order::where(['user_id' => $userId])->orderBy('id', 'DESC')->paginate(10);
        return view('users.account.useraccount', compact('Title', 'shippingAddress', 'ordersList'));
    }

    public function userOrderDetail(Request $request, $orderId)
    {
        $Title = "My Account";
        $userId = Auth::user()->id;
        $EditSlug = OrdersDetail::where(['order_id' => $orderId])->first();
        if (!$EditSlug) {
            $request->session()->flash('errormsg', 'Record Not Found');
            return redirect()->back();
        }
        $orderss = Order::where(['id' => $orderId])->first();
        $ordersDetail = OrdersDetail::where(['order_id' => $orderId])->orderBy('id', 'DESC')->paginate(10);
        return view('users.account.userorderdetail', compact('Title', 'ordersDetail', 'orderss'));
    }

    public function updUserShippingAddress(Request $request)
    {
        if (!empty(Auth::user()->id)) {
            $userId = Auth::user()->id;
        } else {
            $userId = null;
        }
        $countAddress = ShippingAddress::where(['user_id' => $userId])->count();
        if (!empty($countAddress)) {
            if ($request->isMethod('post')) {
                $request->validate([
                    'state' => 'required',
                    'city' => 'required',
                    'pincode' => 'digits:6',
                    'address' => 'required',
                ]);
                ShippingAddress::where('id', $userId)->update([
                    'state' => $request->input('state'),
                    'city' => $request->input('city'),
                    'pincode' => $request->input('pincode'),
                    'address' => $request->input('address'),
                ]);
                return redirect()->back()->with('msg', 'Your shipping address has been update successfully!');
            }
        } else {
            if ($request->isMethod('post')) {
                $request->validate([
                    'state' => 'required',
                    'city' => 'required',
                    'pincode' => 'digits:6',
                    'address' => 'required',
                ]);
                ShippingAddress::create([
                    'user_id' => $userId,
                    'state' => $request->input('state'),
                    'city' => $request->input('city'),
                    'pincode' => $request->input('pincode'),
                    'address' => $request->input('address'),
                    'created_at' => Carbon::now(),
                ]);
                return redirect()->back()->with('msg', 'Your shipping address has been update successfully!');
            }
        }
        return view('users.cart.cart', compact('Title', 'stateList'));
    }

    public function  updateUseAccount(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'first_name' => 'string|max:50',
                'last_name' => 'string|max:50',
                'email' => 'email|max:255|unique:users,email,' . Auth::user()->id . ',id',
                'mobile' => 'digits:10|unique:users,mobile,' . Auth::user()->id . ',id',
                'pincode' => 'digits:6',
            ]);
            $userId = Auth::user()->id;
            if (!empty($userId)) {
                User::where('id', $userId)->update([
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->input('last_name'),
                    'email' => $request->input('email'),
                    'mobile' => $request->input('mobile'),
                    'dob' => $request->input('dob'),
                    'gender' => $request->input('gender'),
                ]);
                $request->session()->flash('msg', 'Profile update successfully.');
                return redirect('my-account');
            } else {
                $request->session()->flash('errormsg', 'You are going wrong please try again');
                return redirect('my-account');
            }
        } else {
            $request->session()->flash('errormsg', 'You are going wrong please try again');
            return redirect('my-account');
        }
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ReferralIncome;
use App\Models\Setting;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'ref_mobile' => ['nullable:mobile','exists:users,mobile','string', 'max:255'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'mobile' => ['required', 'numeric', 'digits:10', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $datetime = date('Y-m-d H:i:s');
        $user = User::create([
            'ref_mobile' => $data['ref_mobile'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(10),
            'created_at' => $datetime,
        ]);
        if (!empty($user)) {
            $settingData = Setting::first();
            if(($settingData->signup_income || $settingData->referrer_income) > 0){
            if (empty($data['ref_mobile'])) {
                 ReferralIncome::create([
                    'user_id' => $user->id,
                    'amount' => $settingData->signup_income,
                    'type' => 'signup income',
                    'income_by' => '',
                ]);
            }else {
                $getreferalUserID = User::where('mobile', $data['ref_mobile'])->first();
                 ReferralIncome::create([
                        'user_id' => $user->id,
                        'amount' => $settingData->signup_income,
                        'type' => 'signup income',
                        'income_by' => $getreferalUserID->id,
                    ]);
                 ReferralIncome::create([
                    'user_id' => $user->id,
                    'amount' => $settingData->referrer_income,
                    'type' => 'referrer income',
                    'income_by' => $getreferalUserID->id,
                ]);
            }
        }
           
        }
        return $user;
    }
}
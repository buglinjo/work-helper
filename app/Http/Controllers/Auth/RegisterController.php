<?php

namespace App\Http\Controllers\Auth;

use App\PayFrequency;
use App\User;
use App\WorkConfig;
use Carbon\Carbon;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;

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

    public function showRegistrationForm()
    {
        $data['payFreq'] = PayFrequency::all();
        return view('auth.register', $data);
    }

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

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
            'name'          => 'required|max:255',
            'email'         => 'required|email|max:255|unique:users',
            'password'      => 'required|min:6|confirmed',
            'timezone'      => 'required',
            'start_date'    => 'required|date',
            'num_of_wdays'  => 'required|numeric|min:1|max:7',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'lunch_start'   => 'required',
            'lunch_end'     => 'required',
            'pay_freq'      => 'required',
            'hourly_wage'   => 'required|numeric',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {

        $user = new User();

        $user->name     = $data['name'];
        $user->email    = $data['email'];
        $user->password = bcrypt($data['password']);

        $user->save();

        $workConfig = new WorkConfig();

        $workConfig->user_id            = $user->id;
        $workConfig->timezone           = $data['timezone'];
        $workConfig->start_date         = Carbon::parse($data['start_date']);
        $workConfig->num_of_workdays    = $data['num_of_wdays'];
        $workConfig->work_day_starts    = $data['start_time'];
        $workConfig->work_day_ends      = $data['end_time'];
        $workConfig->lunch_break_starts = $data['lunch_start'];
        $workConfig->lunch_break_ends   = $data['lunch_end'];
        $workConfig->pay_frequency_id   = $data['pay_freq'];
        $workConfig->hourly_wage        = $data['hourly_wage'];

        $workConfig->save();

        return $user;
    }
}

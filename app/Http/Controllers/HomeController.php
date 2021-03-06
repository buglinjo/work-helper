<?php

namespace App\Http\Controllers;

use App\Device;
use App\PayFrequency;
use App\User;
use App\WorkConfig;
use Carbon\Carbon;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller {

    private $payFrequencyTypes;
    private $name;
    private $timezone;
    private $startDate;
    private $workDayStartsInHours;
    private $workDayEndsInHours;
    private $lunchBreakStarts;
    private $lunchBreakEnds;
    private $numberOfWorkdaysAWeek;
    private $payFrequency;
    private $hourlyWage;

    public function __construct(){

        $this->payFrequencyTypes = PayFrequency::all();

    }

    public function index(){

        $data = [];

        if (\Auth::check()) {
            $data = $this->getData();
        }

        return view('welcome', $data);

    }

    public function getData() {

        $user = \Auth::user();

        $this->name                  = $name                    = explode(' ', $user->name)[0];
        $this->startDate             = $startDate               = $user->workConfig->start_date;
        $this->timezone              = $timezone                = $user->workConfig->timezone;
        $this->workDayStartsInHours  = $workDayStartsInHours    = $user->workConfig->work_day_starts;
        $this->workDayEndsInHours    = $workDayEndsInHours      = $user->workConfig->work_day_ends;
        $this->lunchBreakStarts      = $lunchBreakStartsInHours = $user->workConfig->lunch_break_starts;
        $this->lunchBreakEnds        = $lunchBreakEndsInHours   = $user->workConfig->lunch_break_ends;
        $this->numberOfWorkdaysAWeek = $numberOfWorkDaysAWeek   = $user->workConfig->num_of_workdays;
        $this->payFrequency          = $payFrequency            = $user->workConfig->pay_frequency_id;
        $this->hourlyWage            = $hourlyWage              = $user->workConfig->hourly_wage;

        date_default_timezone_set($this->timezone);

        $startDateTime    = Carbon::createFromFormat('Y-m-d H:i:s', $startDate.' '.$workDayStartsInHours);
        $workWeekStart    = Carbon::today()->startOfWeek();
        $workWeekEnd      = Carbon::today()->startOfWeek()->addDays($numberOfWorkDaysAWeek);
        $workDayStarts    = Carbon::createFromFormat('H:i:s', $workDayStartsInHours);
        $workDayEnds      = Carbon::createFromFormat('H:i:s', $workDayEndsInHours);
        $lunchBreakStarts = Carbon::createFromFormat('H:i:s', $lunchBreakStartsInHours);
        $lunchBreakEnds   = Carbon::createFromFormat('H:i:s', $lunchBreakEndsInHours);

        $now              = Carbon::now();

        $secondsLeftUntilSalary       = 0;
        $secondsLeftUntilLunchBreak   = 0;
        $secondsPassedAfterLunchBreak = 0;
        $todayWorkPercent             = 0;
        $isWorkDay                    = ($now > $workDayStarts->copy()->startOfDay() && $now < $workWeekEnd->copy()->endOfDay()) ? true : false;
        $isWorkTime                   = ($isWorkDay && $now > $workDayStarts && $now < $workDayEnds) ? true : false;
        $isLunchBreak                 = ($isWorkTime && $now > $lunchBreakStarts && $now < $lunchBreakEnds) ? true : false;

        $secondsInWorkDay = $workDayStarts->diffInSeconds($workDayEnds);
        $secondsInPayFreq = $secondsInWorkDay * $numberOfWorkDaysAWeek * $payFrequency;

        $weeksPassed = $now->diffInWeeks($startDateTime->copy()->startOfDay());
        $weekNumber  = $weeksPassed + 1 - (int)($weeksPassed / $payFrequency) * $payFrequency;

        $workDayNumber          = ($weekNumber - 1) * $numberOfWorkDaysAWeek;
        $daysPassedInThisWeek   = $workWeekStart->diffInDays($workDayStarts->copy());

        $daysPassedAfterSalary  = $workDayNumber + $daysPassedInThisWeek;
        $daysLeftUntilEndOfWeek = Carbon::today()->endOfDay()->diffInDays($workWeekEnd);
        $daysLeftUntilSalary    = $daysLeftUntilEndOfWeek + ($numberOfWorkDaysAWeek * ($payFrequency - $weekNumber));

        if ($isWorkDay) {
            $todayWorkDayStarts = $workDayStarts;
            $todayWorkDayEnds   = $workDayEnds;
            if ($isWorkTime) {
                $secondsLeftUntilEndOfDay = $now->diffInSeconds($todayWorkDayEnds);
                $secondsLeftUntilSalary   = $secondsLeftUntilEndOfDay;
            } elseif ($now < $todayWorkDayStarts){
                $secondsLeftUntilEndOfDay = $secondsInWorkDay;
                $daysLeftUntilSalary++;
            } else {
                $secondsLeftUntilEndOfDay = 0;
            }
            $todayWorkPercent = $this->percent($secondsLeftUntilEndOfDay, $secondsInWorkDay);
        }

        $secondsLeftUntilSalary += $daysLeftUntilSalary * $secondsInWorkDay;
        $salaryWorkPercent       = $this->percent($secondsLeftUntilSalary, $secondsInPayFreq);

        $data = [
            'name'                         => $name,
            'today'                        => number_format($todayWorkPercent, 2, ".", "")."%",
            'salary'                       => number_format($salaryWorkPercent, 2, ".", "")."%",
            'daysPassedAfterSalary'        => $daysPassedAfterSalary,
            'isDayNum'                     => $this->getNumFormat($daysPassedAfterSalary+1),
            'daysLeftUntilSalary'          => $daysLeftUntilSalary,
            'isLunchBreak'                 => $isLunchBreak,
            'secondsLeftUntilLunchBreak'   => $secondsLeftUntilLunchBreak,
            'secondsPassedAfterLunchBreak' => $secondsPassedAfterLunchBreak
        ];

        return $data;
    }

    private function percent($num, $whole){

        return 100 - (double)($num * 100 / $whole);

    }

    private function getNumFormat($num){

        $locale = 'en_US';
        $nf = new \NumberFormatter($locale, \NumberFormatter::ORDINAL);

        return $nf->format($num);

    }

    private function pushNotification() {

        $devices = Device::get(['device_token']);
        $message = PushNotification::Message('Message Text',array(
            'badge' => 1,
            'sound' => 'example.aiff',

            'actionLocKey' => 'Action button title!',
            'locKey' => 'localized key',
            'locArgs' => array(
                'localized args',
                'localized args',
            ),
            'launchImage' => 'image.jpg',

            'custom' => array('custom data' => array(
                'we' => 'want', 'send to app'
            ))
        ));

        foreach ($devices as $d) {
            PushNotification::app('web')
                ->to($d->device_token)
                ->send($message);
        }
    }

    public function saveEndpoint(Request $request) {

        $endpoint = $request->input('endpoint');
        $endpoint = explode('/', $endpoint)[5];

        $device = new Device();

        $device->platform = 'web';
        $device->device_token = $endpoint;

        try {
            $device->save();
        } catch (\Exception $e) {
            return abort(503);
        }

        return ['status' => true];

    }

    public function home() {

        $data['payFreq'] = PayFrequency::all();

        return view('home', $data);

    }

    public function updateUser(Request $request) {

        $this->updateUserValidator($request->all())->validate();

        if ($this->updateUserSave($request->all())) {
            return redirect()->route('home');
        }

        return abort(503);

    }

    private function updateUserValidator(array $data) {

        return \Validator::make($data, [
            'name'          => 'required|max:255',
            'email'         => 'required|email|max:255|unique:users,email,'.\Auth::user()->id,
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

    private function updateUserSave(array $data) {

        $authUser   = \Auth::user();
        $workConfig = WorkConfig::where('user_id', $authUser->id)->firstOrFail();
        $user       = User::findOrFail($authUser->id);

        $user->name                     = $data['name'];
        $user->email                    = $data['email'];

        $workConfig->timezone           = $data['timezone'];
        $workConfig->start_date         = Carbon::parse($data['start_date']);
        $workConfig->num_of_workdays    = $data['num_of_wdays'];
        $workConfig->work_day_starts    = $data['start_time'];
        $workConfig->work_day_ends      = $data['end_time'];
        $workConfig->lunch_break_starts = $data['lunch_start'];
        $workConfig->lunch_break_ends   = $data['lunch_end'];
        $workConfig->pay_frequency_id   = $data['pay_freq'];
        $workConfig->hourly_wage        = $data['hourly_wage'];

        try {
            $user->save();
            $workConfig->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;

    }

    public function updatePassword(Request $request) {

        $this->updatePasswordValidator($request->all())->validate();

        if($this->updatePasswordSave($request->all())) {
            return redirect()->route('home');
        }

        return abort(503);

    }

    private function updatePasswordValidator(array $data) {

        $rules = [
            'current_password' => 'required|min:6|matches',
            'password'         => 'required|min:6|confirmed',
        ];

        \Validator::extend('matches', function($attribute, $value, $parameters) {
            return \Hash::check($value, \Auth::user()->password);
        });

        $messages = [ 'matches' => 'Your current password does not match our records.' ];

        return \Validator::make($data, $rules, $messages);

    }

    private function updatePasswordSave(array $data) {

        $user           = User::findOrFail(\Auth::user()->id);
        $user->password = bcrypt($data['password']);

        try {
            $user->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;

    }

}

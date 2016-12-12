<?php

namespace App\Http\Controllers;

use App\Device;
use App\PayFrequency;
use Carbon\Carbon;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller {

    private $payFrequencyTypes;
    private $name;
    private $timeZone;
    private $startDate;
    private $workDayStartsInHours;
    private $workDayEndsInHours;
    private $lunchBreakStarts;
    private $lunchBreakEnds;
    private $numberOfWorkdaysAWeek;
    private $payFrequency;
    private $hourlyWage;

    public function __construct(){

        $this->payFrequencyTypes       = ['Weekly', 'Bi-weekly', 'Semi-monthly', 'Monthly'];
        $this->name                    = 'Irakli';
        $this->startDate               = '2016-10-10';
        $this->timeZone                = 'America/New_York';
        $this->workDayStartsInHours    = '09:00:00';
        $this->workDayEndsInHours      = '18:00:00';
        $this->lunchBreakStarts        = '13:00:00';
        $this->lunchBreakEnds          = '13:30:00';
        $this->numberOfWorkdaysAWeek   = 5;
        $this->payFrequency            = 2;
        $this->hourlyWage              = 13.00;

        date_default_timezone_set($this->timeZone);

    }

    public function getData() {

        $this->payFrequencyTypes = PayFrequency::all();

        if (\Auth::check()) {
            $user = \Auth::user();

            $this->name                    = $user->name;
            $this->startDate               = $user->workConfig->start_date;
            $this->timezone                = $user->workConfig->timezone;
            $this->workDayStartsInHours    = $user->workConfig->work_day_starts;
            $this->workDayEndsInHours      = $user->workConfig->work_day_ends;
            $this->lunchBreakStarts        = $user->workConfig->lunch_break_starts;
            $this->lunchBreakEnds          = $user->workConfig->lunch_break_ends;
            $this->numberOfWorkdaysAWeek   = $user->workConfig->num_of_workdays;
            $this->payFrequency            = $user->workConfig->pay_frequency_id;
            $this->hourlyWage              = $user->workConfig->hourly_wage;

        }

        $name                    = $this->name;
        $startDate               = $this->startDate;
        $workDayStartsInHours    = $this->workDayStartsInHours;
        $workDayEndsInHours      = $this->workDayEndsInHours;
        $lunchBreakStartsInHours = $this->lunchBreakStarts;
        $lunchBreakEndsInHours   = $this->lunchBreakEnds;
        $numberOfWorkDaysAWeek   = $this->numberOfWorkdaysAWeek;
        $payFrequency            = $this->payFrequency;
        $hourlyWage              = $this->hourlyWage;

        $startDateTime    = Carbon::createFromFormat('Y-m-d H:i:s', $startDate.' '.$workDayStartsInHours);
        $workWeekStart    = Carbon::today()->startOfWeek();
        $workWeekEnd      = Carbon::today()->startOfWeek()->addDays($numberOfWorkDaysAWeek);
        $workDayStarts    = Carbon::createFromFormat('H:i:s', $workDayStartsInHours);
        $workDayEnds      = Carbon::createFromFormat('H:i:s', $workDayEndsInHours);
        $lunchBreakStarts = Carbon::createFromFormat('H:i:s', $lunchBreakStartsInHours);
        $lunchBreakEnds   = Carbon::createFromFormat('H:i:s', $lunchBreakEndsInHours);

        $now = Carbon::now();

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

        $workDayNumber = ($weekNumber - 1) * $numberOfWorkDaysAWeek;
        $daysPassedInThisWeek = $workWeekStart->diffInDays($workDayStarts->copy());

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
        $salaryWorkPercent = $this->percent($secondsLeftUntilSalary, $secondsInPayFreq);

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

    public function index(){

        //$this->pushNotification();

        return view('welcome', $this->getData());

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
            \App::abort('404');
        }

        return ['status' => true];

    }

    public function home() {
        return view('home');
    }
}

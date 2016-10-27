<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller
{
    public function index(){
        $name              = 'Irakli';
        $timeZone          = 'America/New_York';
        $startDate         = '2016-10-10';
        $workingDayStarts  = '09:00';
        $recessStarts      = '13:00';
        $recessEnds        = '13:30';
        $workingDayEnds    = '18:00';
        $payFrequencyTypes = ['Weekly', 'Bi-weekly', 'Semi-monthly', 'Monthly'];
        $payFrequency      = 2;
        $salaryPerHour     = 12.00;

        date_default_timezone_set($timeZone);

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$workingDayStarts);

        $bothWeekInSeconds        = 259200;
        $secondsLeftUntilEndOfDay = 0;
        $daysLeftUntilSalary      = 0;

        $workingWeekStart = Carbon::now()->startOfWeek();
        $workingWeekEnd   = Carbon::now()->endOfWeek()->subDays(3)->addSecond();

        $todayWorkingDayStarts = Carbon::createFromFormat('H:i', $workingDayStarts);
        $todayRecessStarts     = Carbon::createFromFormat('H:i', $recessStarts);
        $todayRecessEnds       = Carbon::createFromFormat('H:i', $recessEnds);
        $todayWorkingDayEnds   = Carbon::createFromFormat('H:i', $workingDayEnds);

        $secondsInWorkingDay = $todayWorkingDayStarts->diffInSeconds($todayWorkingDayEnds);

        $now = Carbon::now();

        $weekNumber = $now->diffInWeeks($startDateTime)%2 == 0 ? 1 : 2;

        $daysLeftUntilEndOfWeek = $now->copy()->endOfDay()->diffInDays($workingWeekEnd);
        $daysPassedAfterSalary  = $now->copy()->endOfDay()->diffInDays($workingWeekStart);

        if($weekNumber == 1){
            $daysLeftUntilSalary = $daysLeftUntilEndOfWeek + 4;
        }

        if($now > $todayWorkingDayStarts && $now < $todayWorkingDayEnds){
            $secondsLeftUntilEndOfDay = $now->diffInSeconds($todayWorkingDayEnds);
        }elseif($now < $todayWorkingDayStarts){
            $daysLeftUntilSalary++;
        }

        $secondsLeftUntilSalary        = ($daysLeftUntilSalary * $secondsInWorkingDay) + $secondsLeftUntilEndOfDay;
        $secondsPassedAfterSalary      = $bothWeekInSeconds - $secondsLeftUntilSalary;
        $secondsPassedAfterStartingDay = $secondsInWorkingDay - $secondsLeftUntilEndOfDay;

        $today  = $this->percent($secondsPassedAfterStartingDay, $secondsInWorkingDay);
        $salary = $this->percent($secondsPassedAfterSalary, $bothWeekInSeconds);

        $data['name']                  = $name;
        $data['today']                 = number_format($today, 2, ".", "")."%";
        $data['salary']                = number_format($salary, 2, ".", "")."%";
        $data['daysPassedAfterSalary'] = $daysPassedAfterSalary;
        $data['daysLeftUntilSalary']   = $daysLeftUntilSalary;

        return view('welcome', $data);
    }

    private function percent($passed, $whole){
        return (double)($passed * 100 / $whole);
    }
}

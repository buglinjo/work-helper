<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller
{
    private $payFrequencyTypes;
    private $name;
    private $timeZone;
    private $startDate;
    private $workingDayStarts;
    private $lunchBreakStarts;
    private $lunchBreakEnds;
    private $workingDayEnds;
    private $numberOfWorkdaysAWeek;
    private $payFrequency;
    private $hourlyWage;

    public function __construct(){
        $this->payFrequencyTypes     = ['Weekly', 'Bi-weekly', 'Semi-monthly', 'Monthly'];
        $this->name                  = 'Irakli';
        $this->timeZone              = 'America/New_York';
        $this->startDate             = '2016-10-10';
        $this->workingDayStarts      = '09:00';
        $this->lunchBreakStarts      = '13:00';
        $this->lunchBreakEnds        = '13:30';
        $this->workingDayEnds        = '18:00';
        $this->numberOfWorkdaysAWeek = 4;
        $this->payFrequency          = 2;
        $this->hourlyWage            = 12.00;
    }

    public function index(){
        $name                  = $this->name;
        $timeZone              = $this->timeZone;
        $startDate             = $this->startDate;
        $workingDayStarts      = $this->workingDayStarts;
        $lunchBreakStarts      = $this->lunchBreakStarts;
        $lunchBreakEnds        = $this->lunchBreakEnds;
        $workingDayEnds        = $this->workingDayEnds;
        $numberOfWorkDaysAWeek = $this->numberOfWorkdaysAWeek;
        $payFrequency          = $this->payFrequency;
        $hourlyWage            = $this->hourlyWage;

        date_default_timezone_set($timeZone);

        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$workingDayStarts);

        $secondsLeftUntilEndOfDay     = 0;
        $secondsLeftUntilLunchBreak   = 0;
        $secondsPassedAfterLunchBreak = 0;
        $isLunchBreak                 = false;

        $workingWeekStart = Carbon::today()->startOfWeek();
        $workingWeekEnd   = Carbon::today()->endOfWeek()->subDays(7 - $numberOfWorkDaysAWeek)->addSecond();

        $todayWorkingDayStarts = Carbon::createFromFormat('H:i', $workingDayStarts);
        $todayLunchBreakStarts = Carbon::createFromFormat('H:i', $lunchBreakStarts);
        $todayLunchBreakEnds   = Carbon::createFromFormat('H:i', $lunchBreakEnds);
        $todayWorkingDayEnds   = Carbon::createFromFormat('H:i', $workingDayEnds);

        $secondsInWorkingDay = $todayWorkingDayStarts->diffInSeconds($todayWorkingDayEnds);
        $secondsInPayFreq    = $secondsInWorkingDay * $numberOfWorkDaysAWeek * $payFrequency;

        $now = Carbon::now();

        $weeksPassed = $now->diffInWeeks($startDateTime);
        $weekNumber = $weeksPassed + 1 - (int)($weeksPassed/$payFrequency) * $payFrequency;

        $daysLeftUntilEndOfWeek = Carbon::today()->endOfDay()->diffInDays($workingWeekEnd);
        $daysPassedAfterSalary  = Carbon::today()->endOfDay()->diffInDays($workingWeekStart);

        $daysLeftUntilSalary = $daysLeftUntilEndOfWeek + ($numberOfWorkDaysAWeek * ($payFrequency - $weekNumber));

        if($now > $todayWorkingDayStarts && $now < $todayWorkingDayEnds){
            $secondsLeftUntilEndOfDay = $now->diffInSeconds($todayWorkingDayEnds);
            if($now < $todayLunchBreakStarts){
                $secondsLeftUntilLunchBreak = $now->diffInSeconds($todayLunchBreakStarts);
            }elseif($now > $todayLunchBreakStarts && $now < $todayLunchBreakEnds){
                $isLunchBreak = true;
            }else{
                $secondsPassedAfterLunchBreak = $now->diffInSeconds($todayLunchBreakEnds);
            }
        }elseif($now < $todayWorkingDayStarts){
            $daysLeftUntilSalary++;
        }

        $secondsLeftUntilSalary        = ($daysLeftUntilSalary * $secondsInWorkingDay) + $secondsLeftUntilEndOfDay;
        $secondsPassedAfterSalary      = $secondsInPayFreq - $secondsLeftUntilSalary;
        $secondsPassedAfterStartingDay = $secondsInWorkingDay - $secondsLeftUntilEndOfDay;

        $today  = $this->percent($secondsPassedAfterStartingDay, $secondsInWorkingDay);
        $salary = $this->percent($secondsPassedAfterSalary, $secondsInPayFreq);

        $data = [
            'name'                         => $name,
            'today'                        => number_format($today, 2, ".", "")."%",
            'salary'                       => number_format($salary, 2, ".", "")."%",
            'daysPassedAfterSalary'        => $daysPassedAfterSalary,
            'daysLeftUntilSalary'          => $daysLeftUntilSalary,
            'isLunchBreak'                 => $isLunchBreak,
            'secondsLeftUntilLunchBreak'   => $secondsLeftUntilLunchBreak,
            'secondsPassedAfterLunchBreak' => $secondsPassedAfterLunchBreak
        ];

        return view('welcome', $data);
    }

    private function percent($passed, $whole){
        return (double)($passed * 100 / $whole);
    }
}

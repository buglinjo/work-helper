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
        $this->timeZone                = 'America/New_York';
        $this->startDate               = '2016-10-10';
        $this->workDayStartsInHours    = '09:00';
        $this->workDayEndsInHours      = '18:00';
        $this->lunchBreakStarts        = '13:00';
        $this->lunchBreakEnds          = '13:30';
        $this->numberOfWorkdaysAWeek   = 4;
        $this->payFrequency            = 2;
        $this->hourlyWage              = 12.00;
    }

    public function index(){
        $name                    = $this->name;
        $timeZone                = $this->timeZone;
        $startDate               = $this->startDate;
        $workDayStartsInHours    = $this->workDayStartsInHours;
        $workDayEndsInHours      = $this->workDayEndsInHours;
        $lunchBreakStartsInHours = $this->lunchBreakStarts;
        $lunchBreakEndsInHours   = $this->lunchBreakEnds;
        $numberOfWorkDaysAWeek   = $this->numberOfWorkdaysAWeek;
        $payFrequency            = $this->payFrequency;
        $hourlyWage              = $this->hourlyWage;

        date_default_timezone_set($timeZone);

        $startDateTime    = Carbon::createFromFormat('Y-m-d H:i', $startDate.' '.$workDayStartsInHours);
        $workWeekStart    = Carbon::today()->startOfWeek();
        $workWeekEnd      = Carbon::today()->endOfWeek()->subDays(7 - $numberOfWorkDaysAWeek)->addSecond();
        $workDayStarts    = Carbon::createFromFormat('H:i', $workDayStartsInHours);
        $workDayEnds      = Carbon::createFromFormat('H:i', $workDayEndsInHours);
        $lunchBreakStarts = Carbon::createFromFormat('H:i', $lunchBreakStartsInHours);
        $lunchBreakEnds   = Carbon::createFromFormat('H:i', $lunchBreakEndsInHours);

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
        $weekNumber  = $weeksPassed + 1 - (int)($weeksPassed/$payFrequency) * $payFrequency;

        $daysPassedAfterSalary  = Carbon::today()->endOfDay()->diffInDays($workWeekStart->copy()->subWeeks($weekNumber - 1));
        $daysLeftUntilEndOfWeek = Carbon::today()->endOfDay()->diffInDays($workWeekEnd);

        $daysLeftUntilSalary    = $daysLeftUntilEndOfWeek + ($numberOfWorkDaysAWeek * ($payFrequency - $weekNumber));

        if($isWorkDay){
            $todayWorkDayStarts    = $workDayStarts;
            $todayWorkDayEnds      = $workDayEnds;
            if($isWorkTime){
                $secondsLeftUntilEndOfDay = $now->diffInSeconds($todayWorkDayEnds);
                $secondsLeftUntilSalary = $secondsLeftUntilEndOfDay;
            }elseif($now < $todayWorkDayStarts){
                $secondsLeftUntilEndOfDay = $secondsInWorkDay;
                $daysLeftUntilSalary++;
            }else{
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
            'isDayNum'                     => $this->getNumPlace($daysPassedAfterSalary+1),
            'daysLeftUntilSalary'          => $daysLeftUntilSalary,
            'isLunchBreak'                 => $isLunchBreak,
            'secondsLeftUntilLunchBreak'   => $secondsLeftUntilLunchBreak,
            'secondsPassedAfterLunchBreak' => $secondsPassedAfterLunchBreak
        ];

        return view('welcome', $data);
    }

    private function percent($num, $whole){
        return 100 - (double)($num * 100 / $whole);
    }

    private function getNumPlace($num){
        switch($num){
            case 1:
                return "1st";
                break;
            case 2:
                return "2nd";
                break;
            case 3:
                return "3rd";
                break;
            default:
                return $num."th";
        }
    }
}

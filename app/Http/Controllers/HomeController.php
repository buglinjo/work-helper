<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller
{
    public function index(){
        date_default_timezone_set('America/New_York');

        $startDate = Carbon::create(2016,10,6,18,0,0);
        $secondsInWorkingDay = 32400;
        $bothWeekInSeconds = 259200;
        $secondsLeftUntilEndOfDay = 0;
        $daysLeftUntilSalary = 0;

        $workingWeekStart = Carbon::now()->startOfWeek();
        $workingWeekEnd = Carbon::now()->endOfWeek()->subDays(3)->addSecond();

        $startOfWorkingDay = Carbon::now()->startOfDay()->addHours(9);
        $endOfWorkingDay = Carbon::now()->endOfDay()->subHours(6)->addSecond();

        $now = Carbon::now();

        $weekNumber = $now->diffInWeeks($startDate)%2 == 0 ? 1 : 2;

        $daysLeftUntilEndOfWeek = $now->copy()->endOfDay()->diffInDays($workingWeekEnd);
        $daysPassedAfterSalary = $now->copy()->endOfDay()->diffInDays($workingWeekStart);

        if($weekNumber == 1){
            $daysLeftUntilSalary = $daysLeftUntilEndOfWeek + 4;
        }

        if($now > $startOfWorkingDay && $now < $endOfWorkingDay){
            $secondsLeftUntilEndOfDay = $now->diffInSeconds($endOfWorkingDay);
        }elseif($now < $startOfWorkingDay){
            $daysLeftUntilSalary++;
        }

        $secondsLeftUntilSalary = ($daysLeftUntilSalary * $secondsInWorkingDay) + $secondsLeftUntilEndOfDay;
        $secondsPassedAfterStartingWeek = $bothWeekInSeconds - $secondsLeftUntilSalary;

        $secondsPassedAfterStartingDay = $secondsInWorkingDay - $secondsLeftUntilEndOfDay;

        $salary = (double)($secondsPassedAfterStartingWeek * 100 / $bothWeekInSeconds);
        $today = (double)($secondsPassedAfterStartingDay * 100 / $secondsInWorkingDay);

        $data['today'] = number_format($today, 2, ".", "")."%";
        $data['salary'] = number_format($salary, 2, ".", "")."%";
        $data['daysPassedAfterSalary'] = $daysPassedAfterSalary;
        $data['daysLeftUntilSalary'] = $daysLeftUntilSalary;

        return view('welcome', $data);
    }
}

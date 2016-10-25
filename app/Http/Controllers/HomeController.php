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

        $workingWeekEnd = Carbon::now()->endOfWeek()->subDays(3)->addSecond();

        $startOfWorkingDay = Carbon::now()->startOfDay()->addHours(9);
        $endOfWorkingDay = Carbon::now()->endOfDay()->subHours(6)->addSecond();
        $now = Carbon::now();

        $weekNumber = $now->diffInWeeks($startDate)%2 == 0 ? 1 : 2;

        $daysLeftUntilSalary = $now->copy()->endOfDay()->diffInDays($workingWeekEnd);

        if($weekNumber == 1){
            $daysLeftUntilSalary = $now->copy()->endOfDay()->diffInDays($workingWeekEnd) + 4;
        }

        if($now > $startOfWorkingDay && $now < $endOfWorkingDay){
            $secondsLeftUntilEndOfDay = $now->diffInSeconds($endOfWorkingDay);
        }elseif($now < $startOfWorkingDay){
            $daysLeftUntilSalary++;
        }

        $secondsLeftUntilSalary = ($daysLeftUntilSalary * $secondsInWorkingDay) + $secondsLeftUntilEndOfDay;
        $secondsPassedAfterStartingWeek = $bothWeekInSeconds - $secondsLeftUntilSalary;

        $percent = (double)($secondsPassedAfterStartingWeek * 100 / $bothWeekInSeconds);
        $data['percent'] = number_format($percent, 2, ".", "")."%";

        return view('welcome', $data);
    }
}

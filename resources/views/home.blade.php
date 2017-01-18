@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <passport-clients></passport-clients>
            <passport-authorized-clients></passport-authorized-clients>
            <passport-personal-access-tokens></passport-personal-access-tokens>
        </div>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Settings</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('home') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>
                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ Auth::user()->name }}" required autofocus>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ Auth::user()->email }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('timezone') ? ' has-error' : '' }}">
                            <label for="timezone" class="col-md-4 control-label">Timezone</label>
                            <div class="col-md-6">
                                <select id="timezone" name="timezone" class="form-control">
                                    <option value="">Timezone</option>
                                    @foreach(timezone_identifiers_list() as $t)
                                        @if (Auth::user()->workConfig->timezone == $t)
                                            <option value="{{ $t }}" selected>{{ $t }}</option>
                                        @else
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('timezone'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('timezone') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('start_date') ? ' has-error' : '' }}">
                            <label for="start-date" class="col-md-4 control-label">Work Start Date</label>
                            <div class="col-md-6">
                                <input name="start_date" id="start-date" type="text" class="form-control date" value="{{ \Carbon\Carbon::parse(Auth::user()->workConfig->start_date)->format('m/d/Y') }}"/>
                                @if ($errors->has('start_date'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('start_date') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('num_of_wdays') ? ' has-error' : '' }}">
                            <label for="num-of-wdays" class="col-md-4 control-label">Number Of Work Days A Week</label>
                            <div class="col-md-6">
                                <input name="num_of_wdays" id="num-of-wdays" type="number" class="form-control" value="{{ Auth::user()->workConfig->num_of_workdays }}"/>
                                @if ($errors->has('num_of_wdays'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('num_of_wdays') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('start_time') || $errors->has('end_time')? ' has-error' : '' }}">
                            <label for="work-hours" class="col-md-4 control-label">Work Hours</label>
                            <div class="col-md-6">
                                <div class="input-daterange input-group">
                                    <input id="work-hours" type="text" class="form-control time" name="start_time" value="{{ Auth::user()->workConfig->work_day_starts }}"/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control time" name="end_time" value="{{ Auth::user()->workConfig->work_day_ends }}"/>
                                </div>
                                @if ($errors->has('start_time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('start_time') }}</strong>
                                    </span>
                                @elseif ($errors->has('end_time'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('end_time') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('lunch_start') || $errors->has('lunch_end') ? ' has-error' : '' }}">
                            <label for="lunch-time" class="col-md-4 control-label">Lunch Time</label>
                            <div class="col-md-6">
                                <div class="input-daterange input-group">
                                    <input id="lunch-time" type="text" class="form-control time" name="lunch_start" value="{{ Auth::user()->workConfig->lunch_break_starts }}"/>
                                    <span class="input-group-addon">to</span>
                                    <input type="text" class="form-control time" name="lunch_end" value="{{ Auth::user()->workConfig->lunch_break_ends }}"/>
                                </div>
                                @if ($errors->has('lunch_start'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lunch_start') }}</strong>
                                    </span>
                                @elseif ($errors->has('lunch_end'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('lunch_end') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('pay_freq') ? ' has-error' : '' }}">
                            <label for="pay-freq" class="col-md-4 control-label">Payment Frequency</label>
                            <div class="col-md-6">
                                <select id="pay-freq" name="pay_freq" class="form-control">
                                    <option value="">Payment Frequency</option>
                                    @foreach($payFreq as $p)
                                        @if (Auth::user()->workConfig->pay_frequency_id == $p->id)
                                            <option value="{{ $p->id }}" selected>{{ $p->name }}</option>
                                        @else
                                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @if ($errors->has('pay_freq'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('pay_freq') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('hourly_wage') ? ' has-error' : '' }}">
                            <label for="hourly-wage" class="col-md-4 control-label">Hourly Wage ($)</label>
                            <div class="col-md-6">
                                <input id="hourly-wage" name="hourly_wage" class="form-control" type="number" value="{{ Auth::user()->workConfig->hourly_wage }}"/>
                                @if ($errors->has('hourly_wage'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('hourly_wage') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Save
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Password Reset</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('update.password') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                            <label for="current-password" class="col-md-4 control-label">Current Password</label>
                            <div class="col-md-6">
                                <input id="current-password" type="password" class="form-control" name="current_password" required>
                                @if ($errors->has('current_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('current_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">New Password</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

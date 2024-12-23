<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('inventory:update')->everyMinute();

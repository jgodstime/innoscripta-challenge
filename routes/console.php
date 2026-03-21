<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:get-article-command')->everyFifteenMinutes();

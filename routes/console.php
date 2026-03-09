<?php

use Illuminate\Support\Facades\Schedule;

// === REMINDER SYSTEM ===
// Send H-1 and overdue reminder emails every day at 7 AM WIB
Schedule::command('reminders:send-overdue')->dailyAt('07:00');

// === AI ANALYSIS ===
// Run AI suggestion analysis every day at 8 AM
Schedule::command('ai:run-analysis')->dailyAt('08:00');

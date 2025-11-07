<?php
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// Run with: php artisan tinker or as a custom command

// For each student, set students.user_id to users.user_id where students.user_id matches users.id
DB::table('students')
    ->join('users', 'students.user_id', '=', 'users.id')
    ->update(['students.user_id' => DB::raw('users.user_id')]);

// Output: All students.user_id now matches users.user_id

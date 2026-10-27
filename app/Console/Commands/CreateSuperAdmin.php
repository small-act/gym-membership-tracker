<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    protected $signature = 'app:create-super-admin';

    protected $description = 'Create the first Gym Membership Tracker Super Admin';

    public function handle(): int
    {
        if (User::where('role', 'super_admin')->exists()) {
            $this->error('A Super Admin account already exists.');
            return self::FAILURE;
        }

        $name = trim($this->ask('Super Admin full name'));
        $email = strtolower(trim($this->ask('Super Admin email address')));

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Enter a valid name and email address.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('That email address is already registered.');
            return self::FAILURE;
        }

        $password = $this->secret('Enter password (minimum 12 characters)');
        $confirmation = $this->secret('Confirm password');

        $validator = Validator::make([
            'password' => $password,
        ], [
            'password' => ['required', 'string', 'min:12'],
        ]);

        if ($validator->fails() || $password !== $confirmation) {
            $this->error('Passwords must match and contain at least 12 characters.');
            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role = 'super_admin';
        $user->save();

        $this->info('Super Admin account created successfully.');
        $this->line('Email: ' . $email);
        $this->line('Role: Super Admin');
        $this->line('You can now sign in using your email and password.');

        return self::SUCCESS;
    }
}

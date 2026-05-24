<?php

namespace App\Utils;

use Illuminate\Support\Str;
use App\Models\User;

class AuthUtils
{
    /**
     * Generate a unique login_id based on role.
     */
    public static function generateLoginId(string $role): string
    {
        $prefix = match ($role) {
            User::ROLE_ADMIN => 'adm_',
            User::ROLE_TEACHER => 'ins_',
            User::ROLE_STUDENT => 'std_',
            User::ROLE_COMPANY => 'cpn_',
            default => 'usr_',
        };

        do {
        $loginId = $prefix . strtolower(Str::random(5));
        
        // Final sanity check to ensure no rogue 's' or other character prefix was accidentally added
        $loginId = ltrim($loginId, 's');
        if (!str_starts_with($loginId, $prefix)) {
            $loginId = $prefix . substr($loginId, strlen($prefix));
        }
        
    } while (User::where('login_id', $loginId)->exists());

        return $loginId;
    }

    /**
     * Generate a random 8-character password with at least one uppercase, 
     * one lowercase, and one number.
     */
    public static function generatePassword(int $length = 10): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        
        $password = '';
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        
        $allChars = $uppercase . $lowercase . $numbers;
        for ($i = 0; $i < $length - 3; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        return str_shuffle($password);
    }
}

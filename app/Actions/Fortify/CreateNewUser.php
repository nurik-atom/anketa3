<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Candidate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'email.unique' => 'Этот email уже зарегистрирован.',
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        // Проверяем, есть ли кандидат с таким же email
        $candidate = Candidate::where('email', $input['email'])->first();
        
        if ($candidate) {
            // Связываем существующего кандидата с новым пользователем
            $candidate->update(['user_id' => $user->id]);
            
            Log::info('Candidate linked to new user', [
                'candidate_id' => $candidate->id,
                'user_id' => $user->id,
                'email' => $input['email']
            ]);
        }

        return $user;
    }
}

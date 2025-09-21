<?php

namespace App\Actions\Fortify;

use Illuminate\Auth\Events\PasswordResetLinkRequested;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class RequestPasswordResetLink
{
    /**
     * The password broker implementation.
     *
     * @var \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected $broker;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\PasswordBroker  $broker
     * @return void
     */
    public function __construct(PasswordBroker $broker)
    {
        $this->broker = $broker;
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        $request->validate([Fortify::email() => 'required|email']);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = $this->broker->sendResetLink(
            $request->only(Fortify::email())
        );

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Ссылка на сброс пароля была отправлена на ' . $request->email)
                        ->with('reset_email', $request->email);
        }

        // If an error was returned by the password broker, we will get this message
        // translated so we can notify a user of the problem. We'll redirect back
        // to where the users came from with their error message.
        throw ValidationException::withMessages([
            Fortify::email() => [trans($status)],
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function forgot()
    {
        return view('forgot');
    }

    public function resetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'No user found with that email.']);
        }

        $token = Password::getRepository()->create($user);
        // Alternatively: $token = Password::broker()->createToken($user);

        // 3) (Optional) Build the URL yourself
        $resetUrl = url(route('v1.password.reset', [
            'token' => $token,
            'email' => $user->getEmailForPasswordReset(),
        ], false));
        $data = [
            'name' => $user->name,
            'resetLink' => $resetUrl,
        ];

        Mail::to($request->email)->send(new ResetPasswordMail($data));

        return back()->with('status', 'Please check your inbox, we have sent you an email');
    }

    public function createNewPassword(Request $request, $token)
    {
        $email = $request->input('email');

        return view('reset', compact('email', 'token'));
    }

    public function saveNewPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('v1.login')->with('status', __($status));
        } else {
            return back()->with('status', 'your token has expired');
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->user_status == 1) {
                // Custom logic after successful login
                return redirect()->intended('/v1/dashboard')->with('success', 'Welcome back!');
            } else {
                return back()->withErrors(['email' => 'Your account deactivated'])->withInput();
            }

        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return redirect('/login');
    }
}

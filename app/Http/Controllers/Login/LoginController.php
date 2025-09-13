<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    #login view
    public function login()
    {
        return view('login.login');
    }

    #validate user
    public function validateUser(LoginRequest $request)
    {
        try {
            $credentials = [
                'email' => trim(strtolower($request->input('email'))),
                'password' => $request->input('password'),
            ];
            #validate user credentials
            if (Auth::attempt($credentials) && Auth::user()->status == 1) {
                return redirect()->route('dashboard')->with(['message' => 'Login Successfully', 'alert-type' => 'success']);
            } else {
                return redirect()->back()->with(['message' => 'Invalid credentials', 'alert-type' => 'error'])->withInput();
            }
        } catch (Exception $e) {
            Log::channel('exception')->error('validateUser: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'An error occurred while processing your request.', 'alert-type' => 'error'])->withInput();
        }
    }

    #forget password view
    public function forgetPassword()
    {
        return view('login.forget_password');
    }

    function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login')->with(['message' => 'Logout Successfully', 'alert-type' => 'success']);
    }

    public function sendResetLink(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validate->fails()) {
                return redirect()->back()->with(['message' => $validate->errors()->first(), 'alert-type' => 'error'])->withInput();
            }
            $userDetail = User::where(['email' => $request->email, 'status' => 1])->first();
            if (empty($userDetail)) {
                return redirect()->route('login')->with(['message' => 'We\'re unable to send the reset link, Kindly contact the administration department', 'alert-type' => 'error']);
            }
            #email
            $receiverEmail = $request->email;
            // Generate secure token
            $token = Password::createToken($userDetail);
            $resetLink = route('reset.password', ['token' => $token, 'email' => $request->email]);
            #send Email
            Mail::to($receiverEmail)->queue(new ResetPasswordMail($resetLink));
            return redirect()->route('login')->with([
                'message' => 'A password reset link has been sent to your email.',
                'alert-type' => 'success'
            ]);
        } catch (Exception $e) {
            Log::channel('exception')->error('sendResetLink: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'An error occurred while sending a reset link, Kindly contact the administration department', 'alert-type' => 'error'])->withInput();
        }
    }

    public function resetPassword($token)
    {
        return view('login.reset-password', ['token' => $token, 'email' => request('email')]);
    }

    #update login password
    public function updatePassword(Request $request)
    {
        try {
            $validate = Validator::make($request->all(), [
                'token' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:8|confirmed',
            ]);
            if ($validate->fails()) {
                return redirect()->back()->with(['message' => $validate->errors()->first(), 'alert-type' => 'error'])->withInput();
            }
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ]);
                    // ])->setRememberToken(Str::random(60));
                    $user->save();
                    event(new PasswordReset($user));
                }
            );
            return $status === Password::PasswordReset
                ? redirect()->route('login')->with(['message' => 'Password reset successfull', 'alert-type' => 'success'])
                : redirect()->route('login')->with(['message' => 'Reset link either expired or contact the administration department', 'alert-type' => 'error']);
        } catch (Exception $e) {
            Log::channel('exception')->error('sendResetLink: ' . $e->getMessage());
            return redirect()->back()->with(['message' => 'An error occurred while updating a password, Kindly contact the administration department', 'alert-type' => 'error'])->withInput();
        }
    }
}

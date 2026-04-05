<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        try {

            if (Auth::check()) {
                $user = Auth::user();
                $message = 'Welcome';

                if ($user->role == UserRole::ADMIN) {
                    return redirect()
                        ->route('admin.dashboard.index')
                        ->with('success', $message);
                } elseif ($user->role == UserRole::DC) {
                    return redirect()
                        ->route('dc.dashboard.index')
                        ->with('success', $message);
                } elseif ($user->role == UserRole::TAG_OFFICER) {
                    return redirect()
                        ->route('tag-officer.dashboard.index')
                        ->with('success', $message);
                } elseif ($user->role == UserRole::UNO) {
                    return redirect()
                        ->route('uno.dashboard.index')
                        ->with('success', $message);
                } else {
                    Auth::logout();

                    return redirect()
                        ->route('login')
                        ->with('error', 'access denie');
                }
            }

            return view('auth.login');
        } catch (\Throwable $error) {
            Auth::logout();

            return redirect()->route('login')->with('error', $error->getMessage());
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'identifier' => 'required|string', // email or phone
                'password' => 'required|string|min:6',
            ], [
                'identifier.required' => 'email or phone is required',
                'password.required' => 'password is required',
                'password.min' => 'password must be at least 6 characters',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $identifier = $request->input('identifier');
            $password = $request->input('password');
            $fieldType = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

            $credentials = [
                $fieldType => $identifier,
                'password' => $password,
            ];

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();
                $user = Auth::user();

                if ($user->status == 'inactive') {
                    Auth::logout();

                    return redirect()->route('login')
                        ->with('error', 'Your account is not active. Please contact admin.')
                        ->withInput();
                }

                if ($user->role == UserRole::ADMIN) {
                    return redirect()->route('admin.dashboard.index')
                        ->with('success', 'Login successfully');
                } elseif ($user->role == UserRole::DC) {
                    return redirect()->route('dc.dashboard.index')
                        ->with('success', 'Login successfully');
                } elseif ($user->role == UserRole::TAG_OFFICER) {
                    return redirect()->route('tag-officer.dashboard.index')
                        ->with('success', 'Login successfully');
                } elseif ($user->role == UserRole::UNO) {
                    return redirect()->route('uno.dashboard.index')
                        ->with('success', 'Login successfully');
                } else {
                    Auth::logout();

                    return redirect()->route('login')
                        ->with('error', 'Access denied')->withInput();
                }
            }

            return redirect()->back()
                ->with('error', 'Invalid credential')
                ->withInput();
        } catch (\Exception $error) {
            return redirect()->back()
                ->with('error', 'Something went wrong. Try again later.')
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->with('success', 'Logout Successful');
        } catch (\Exception $error) {
            return redirect()->route('login')->with('error', $error->getMessage());
        }
    }
}

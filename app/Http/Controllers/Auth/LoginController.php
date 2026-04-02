<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        try {

            if (Auth::check()) {
                $user = Auth::user();
                $message = 'Welcome';

                if ($user->role === UserRole::ADMIN) {
                    return redirect()
                        ->route('admin.dashboard.index')
                        ->with('success', $message);
                } elseif ($user->role === UserRole::DC) {
                    return redirect()
                        ->route('dc.dashboard.index')
                        ->with('success', $message);
                } elseif ($user->role === UserRole::TAG_OFFICER) {
                    return redirect()
                        ->route('tag-officer.dashboard.index')
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
}

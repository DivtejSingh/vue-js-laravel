<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SadminController extends Controller
{
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::guard('sadmin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->get('remember')
        )) {
            return redirect()->route('sadmin.dashboard');
        } else {
            session()->flash('error', 'Either Email or Password not Matched');
            return back()->withInput($request->only('email'));
        }
    }

    public function dashboard()
    {
        return view('sadmin.dashboard');
    }

    public function logout()
    {
        Auth::guard('sadmin')->logout();
        return redirect()->route('sadmin.login');
    }

}

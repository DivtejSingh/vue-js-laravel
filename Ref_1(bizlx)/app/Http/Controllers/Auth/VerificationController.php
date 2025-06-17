<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyEmail($token)
    {
        // Check if the token exists
        $verification = DB::table('user_verifications')->where('token', $token)->first();

        if (!$verification) {
            return response()->json(['message' => 'Invalid verification token.'], 400);
        }

        // Find the user and verify status
        $user = User::find($verification->user_id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        if ($user->user_status == 1) {
            return response()->json(['message' => 'User already verified.'], 400);
        }

        // Mark user as verified
        $user->user_status = 1;
        $user->save();

        // Delete token after successful verification
        DB::table('user_verifications')->where('token', $token)->delete();

        return redirect('/login')->with('message', 'Your email has been verified. Please login.');
    }
}

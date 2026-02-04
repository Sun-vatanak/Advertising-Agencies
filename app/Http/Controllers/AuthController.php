<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuthResource;
use App\Mail\ForgotPassMail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\ResetPasswordOtp;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $req->validate([
            'email' => ['required', 'email', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255', 'confirmed'],
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50']
        ]);

        $user = new User();
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->role_id = 4;
        $user->is_active = 1;
        $user->save();

        $user->profile()->create([
            'first_name' => $req->input('first_name'),
            'last_name' => $req->input('last_name'),
        ]);

        $token = $user->createToken('API Token')->plainTextToken;
        $user->token = $token;
        $userData = new AuthResource($user);
        $userData->additional(['token' => $token]);

        return response()->json([
            'result'  => true,
            'message' => 'Login successful',
            'data'    => new AuthResource($user),
        ]);
    }



    public function login(Request $req)
    {
        $req->validate([
            'email_or_phone' => ['required'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $req->email_or_phone)
            ->orWhereHas('profile', function ($query) use ($req) {
                $query->where('phone', $req->email_or_phone);
            })
            ->first();

        if (!$user || !Hash::check($req->password, $user->password)) {
            return response()->json([
                'result' => false,
                'message' => 'Incorrect email or phone or password.',
                'data' => []
            ]);
        }

        if ($user->is_active == 0) {
            return response()->json([
                'result' => false,
                'message' => 'Your account is inactive. Please contact support.',
                'data' => []
            ]);
        }

      
        // Inside public function login(Request $req) ...

        $token = $user->createToken('API Token')->plainTextToken;

        // Attach token to the model so AuthResource can see it
        $user->token = $token;

        return response()->json([
            'result'  => true,
            'message' => 'Login successful',
            // Pass the user (who now has the token property attached)
            'data'    => new AuthResource($user),
        ]);
    }



    public function logout(Request $request)
    {
        $request->user('sanctum')->currentAccessToken()->delete();
        return response()->json([
            'result' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function forgotPassword(Request $req)
    {
        $req->validate([
            'email' => ['required', 'email', 'max:250']
        ]);

        $user = User::where('email', $req->email)->first();
        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Email not found.',
            ]);
        }

        $otp = strtoupper(Str::random(6));
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->email, $otp, now()->addMinutes(15));
        // Mail::to($req->input('email'))
        //     ->queue(new ForgotPassMail($otp));

        return response()->json([
            'result' => true,
            'message' => 'OTP sent to your email.'
        ]);
    }

    public function verifyOTP(Request $req)
    {
        $req->validate([
            'email' => ['required', 'email', 'max:250'],
            'otp' => ['required', 'numeric'],
        ]);

        $user = User::where('email', $req->email)->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Email not found.'
            ]);
        }

        $cachedOtp = Cache::get('otp_' . $user->email);

        if (!$cachedOtp) {
            return response()->json(['message' => 'OTP expired or invalid.']);
        }

        if ($cachedOtp != $req->otp) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid OTP.'
            ]);
        }
        return response()->json(
            [
                'result' => true,
                'message' => 'OTP verified successfully. You can now reset your password.'
            ]
        );
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:250'],
            'otp' => ['required', 'numeric'],
            'new_password' => ['required', 'string', 'max:255', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Email not found.'
            ]);
        }

        $cachedOtp = Cache::get('otp_' . $user->email);
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'result' => false,
                'message' => 'OTP expired or invalid.'
            ]);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        Cache::forget('otp_' . $user->email);
        return response()->json([
            'result' => true,
            'message' => 'Password reset successfully.'
        ]);
    }
}

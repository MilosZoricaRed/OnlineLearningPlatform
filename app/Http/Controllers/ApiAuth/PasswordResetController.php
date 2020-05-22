<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetRequest;
use App\Mail\PasswordResetSuccess;
use App\PasswordReset;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mail;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
        	return $this->getErrorResponse("We can't find a user with that e-mail address.", 404);
       	}

       	// delete reset password if exists.
       	$passwordReset = PasswordReset::where('email', $user->email);
       	if($passwordReset->count()) {
       		$passwordReset->delete();
       	}

   		$passwordReset = new PasswordReset;
   		$passwordReset->email = $user->email;
       	$passwordReset->created_at = now()->format('Y-m-d H:i:s');
       	$passwordReset->token = str_random(60);
       	$passwordReset->save();

        if ($user && $passwordReset) {
           
            Mail::to($user->email)->send(new PasswordResetRequest($user, $passwordReset->token));
           
        }
      
        return response()->json('We have e-mailed your password reset link!', 200);
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
    	$passwordReset = PasswordReset::where('token', $token)->first();
        if (!$passwordReset)
        	return $this->getErrorResponse('This password reset token is invalid.', 404);
        if (Carbon::parse($passwordReset->created_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return $this->getErrorResponse('This password reset token is invalid.', 404);
        }
        return $this->respond($passwordReset->toArray());
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        
        if (!$passwordReset) {
         	return $this->getErrorResponse('This password reset token is invalid.', 404);
        }

        $user = User::where('email', $passwordReset->email)->first();
        if (!$user) {
        	return $this->getErrorResponse("We can't find a user with that e-mail address.", 404);
        }

        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset = PasswordReset::where('email', $user->email)->delete();

        Mail::to($user->email)->send(new PasswordResetSuccess($user));
        
        return $this->respond($user->toArray());
    }
}

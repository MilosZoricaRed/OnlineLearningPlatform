<?php

namespace App\Http\Controllers;

use App\User;
use App\Classes\Utils;
use App\Companys;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use PhpParser\Node\Expr\Variable;

class RegisterController extends Controller
{

    public function register(Request $request)
    {

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $email_extension = Companys::where('id', $request->company_id)->first();
        // return response()->json($email_extension);
        $check['user_extension'] = substr($request->email, strpos($request->email, '@') + 1);
        //return response()->json($check['user_extension'] == $email_extension['email_extension']);

        $user = new User();
        $user->name = $request->name;
        if ($check['user_extension'] == $email_extension['email_extension']) {
            $user->email = $request->email;
        } else {
            return response()->json("This domain is not registrated!");
        }
        $user->password = bcrypt($request->password);
        if ($request) {
            $user->icon = $request->icon;
        } else {
            $user->icon = "";
        }
        $user->position = $request->position;
        $user->company_id = $request->company_id;
        $user->gender = $request->gender;
        $user->sector_id = $request->sector_id;
        $user->save();


        $http = new \GuzzleHttp\Client();

        $response = $http->post(Utils::$MAINURL_address . '/oauth/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'zKE4Ilr1BaCfvCqDlsGAhCgRvxAAB4ikGDaMcYfV',
                'username' => $request->email,
                'password' => $request->password,
                'scope' => '',
            ]
        ]);

        //return response(url($user));
        return response(['data' => json_decode((string) $response->getBody(), true)]);
    }


    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found!']);
        }
        if (Hash::check($request->password, $user->password)) {



            $http = new \GuzzleHttp\Client();

            $response = $http->post(Utils::$MAINURL_address . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => '2',
                    'client_secret' => 'zKE4Ilr1BaCfvCqDlsGAhCgRvxAAB4ikGDaMcYfV',
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ]
            ]);

            //return response(url($user));
            return response(['data' => json_decode((string) $response->getBody(), true)]);
        }
    }
    
    public function sendPasswordReset(Request $request)
    {
        $email = $request->email;
        $check = User::where('email', $email)->first();
       
        if ($check) {
            $memberData = array(
                'email' => $check->email,
                'name' => $check->name,
                'from' => 'porudzbinelgmaster@gmail.com'
            );
            $test_password = str_random(15);
            $hash = "kojotmilos@hotmail.com=123456";
            $data = array(
                //'test_password' => $test_password,
                'link' => 'api/reset/password/' . $hash
            );

             //$this->check = $user;
            // $this->passwordReset($request, $email);

            Mail::send('emails.mail', $data, function ($message) use ($memberData) {
                $message->from($memberData['from'], 'Digital Spark Support');
                $message->to($memberData['email'], $memberData['name'])->subject('Digital Spark');
            });
        }
        return url("reset/password/email/{$test_password}");
    }

    public function passwordReset(Request $request, $hash)
    { 
        echo $hash;
        /*$request->validate([
            'password' => ['required', 'confirmed']
        ]);

        $check = User::where('email', )->first();
        if ($check) {
            $check->password = $request->password;
            $check->save();
        }
        return response()->json('ok', 200);*/
    }
}

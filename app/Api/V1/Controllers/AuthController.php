<?php

namespace App\Api\V1\Controllers;

use Mail;
use JWTAuth;
use Validator;
use Config;

use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Dingo\Api\Exception\ValidationHttpException;

use App\User;
use App\UserReset;

class AuthController extends Controller
{
    use Helpers;

    public function check(){
      if (! $user = JWTAuth::parseToken()->authenticate()){
         return response()->json(['user_not_found'], 404);
      }else{
         return array_only($user->toArray(),['name','surname']);
      }

    }

    public function me(){
      if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['user_not_found'], 404);
        }else{
          return array_except($user->toArray(),['id','profile_photo','status','register_from','token'
            ,'created_at','updated_at','updatedby_adminid','updatedby_adminname','deleted_at']);
        }
    }

    public function login(Request $request)
    {


        $credentials = $request->only(['username', 'password']);

        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->response->errorUnauthorized();
            }
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token', 500);
        }


        return response()->json(compact('token'));

    }

    public function signup(Request $request)
    {


        //return 'ok';
        $signupFields = [
            'user_type','name','surname','birthday','country_id','province_id'
           ,'district_id','amphur_id','postcode', 'gender','phone', 'email', 'password','address_no'
           ,'mooban', 'street','marital_status', 'children_no' , 'education', 'family_income', 'career' ,'career_other'
           ,'position','nationality','username'
        ];


        $userData = $request->only($signupFields);

        if($request->input('user_type') == 1){
          $userData['id_card'] = $request->input('id_card');
        }else{
          $userData['id_card'] = $request->input('id_card_2');
        }

        $checker = [
          'user_type' => 'required',
          'id_card' => 'required|unique:users,id_card,0,id,deleted_at,NULL',
          'name' => 'required',
        	'surname' => 'required',
        	'country_id' => 'required',
        	'province_id' => 'required',
        	'district_id' => 'required',
        	'postcode' => 'required',
        	'gender' => 'required',
        	'phone' => 'required',
        	'email' => 'email',
        	'username' => 'required|unique:users,username,0,id,deleted_at,NULL',
        	'password' => 'required|min:6'
        ];

        $validator = Validator::make($userData, $checker );

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }


        User::unguard();

        //$userData['token'] = hash_hmac('sha256', str_random(40), config('app.key'));
        $userData['status'] = 'A';
        $userData['birthday'] = $request->input('years'). '-' . $request->input('months'). '-' . $request->input('days');
        $userData['register_from'] = 'w';

        $userData['password'] = bcrypt($userData['password']);

        $user = User::create($userData);
        User::reguard();

        if(!$user->id) {
            return $this->response->error('could_not_create_user', 500);
        }

        if($user->email != ''){
          Mail::send('emails.registersuccess', ['user' => $user], function ($m) use ($user) {
              $m->from('admin@houseofticket.com', 'HouseOfTicket');
              $m->to($user->email, $user->name)->subject('ยืนยันการลงทะเบียน House Of Ticket');
          });
        }

        return $this->response->created();
    }

    public function update(Request $request)
    {


        //return 'ok';
        $signupFields = [
            'name','surname','birthday','country_id','province_id'
           ,'district_id','amphur_id','postcode', 'gender','phone', 'email', 'password','address_no'
           ,'mooban', 'street'
        ];


        $userData = $request->only($signupFields);

        if($request->input('user_type') == 1){
          $userData['id_card'] = $request->input('id_card');
        }else{
          $userData['id_card'] = $request->input('id_card_2');
        }

        $checker = [
          'name' => 'required',
        	'surname' => 'required',
        	'country_id' => 'required',
        	'province_id' => 'required',
        	'district_id' => 'required',
        	'postcode' => 'required',
        	'gender' => 'required',
        	'phone' => 'required',
        	'email' => 'email',
        ];

        $validator = Validator::make($userData, $checker );

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $user = JWTAuth::parseToken()->authenticate();



        $user->birthday     = $request->input('years'). '-' . $request->input('months'). '-' . $request->input('days');
        $user->name         = $request->input('name');
        $user->surname      = $request->input('surname');
        $user->country_id   = $request->input('country_id');
        $user->province_id  = $request->input('province_id');
        $user->amphur_id    = $request->input('amphur_id');
        $user->district_id  = $request->input('district_id');
        $user->postcode     = $request->input('postcode');
        $user->gender       = $request->input('gender');
        $user->email        = $request->input('email');
        $user->nationality  = $request->input('nationality');
        $user->phone        = $request->input('phone');

        $user->address_no   = $request->input('address_no');
        $user->mooban       = $request->input('mooban');
        $user->street       = $request->input('street');


        $user->save();

        if(!$user->id) {
            return $this->response->error('could_not_update_user', 500);
        }

        return $this->response->created();
    }

    public function recovery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_card' => 'required',
            'days' => 'required',
            'months' => 'required',
            'years' => 'required',
        ]);




        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $birthday = $request->input('years').'-'.$request->input('months').'-'.$request->input('days');

        $user = User::where('id_card',$request->input('id_card'))
                ->where('birthday',$birthday)->first();

        $email = "";
        if($user){
          $email = $user->email == ''?'':$user->email;

          if($email == ''){
            $v = Validator::make(['email'=> $user->username],['email' => 'required|email' ]);
            if($v->fails()){
              return $this->response->error('1', 404);
            }
            $email = $user->username;
          }

        }

        if($email == ''){
          return $this->response->error('2', 404);
        }else{
          $userReset = UserReset::where('user_id',$user->id)->first();

          if(!$userReset){
            $userReset = new UserReset;
            $userReset->user_id = $user->id;
          }

          $userReset->token = hash_hmac('sha256', str_random(40) . $user->id , config('app.key'));
          $userReset->save();

          Mail::send('emails.password_reset', ['user' => $user,'userReset' => $userReset], function ($m) use ($user) {
              $m->from('admin@houseofticket.com', 'HouseOfTicket');
              $m->to($user->email, $user->name)->subject('แจ้งการเปลี่ยนแปลงรหัสผ่าน House Of Ticket.');
          });

          return "true";
        }
    }

    public function reset(Request $request)
    {
        $credentials = $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );

        $validator = Validator::make($credentials, [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        if($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $response = Password::reset($credentials, function ($user, $password) {
            $user->password = $password;
            $user->save();
        });

        switch ($response) {
            case Password::PASSWORD_RESET:
                if(Config::get('boilerplate.reset_token_release')) {
                    return $this->login($request);
                }
                return $this->response->noContent();

            default:
                return $this->response->error('could_not_reset_password', 500);
        }
    }

    public function checkemail(Request $request){
      //return  $request->input('email');
      if(User::where('username',$request->input('username'))->count() > 0){
        return 'false';
      }
      return 'true';

    }
    public function check_id_card(Request $request){
      //return  $request->input('email');
      if(User::where('id_card',$request->input('id_card'))->count() > 0){
        return 'false';
      }
      return 'true';

    }

    public function sendmail(){
      $user =  User::find(1);
      //return view('emails.registersuccess')->with('user',$user);
      if($user->email != ''){
        Mail::send('emails.registersuccess', ['user' => $user], function ($m) use ($user) {
            $m->from('admin@houseofticket.com', 'HouseOfTicket');
            $m->to($user->email, $user->name)->subject('ยืนยันการลงทะเบียน House Of Ticket');
        });
      }
    }


}

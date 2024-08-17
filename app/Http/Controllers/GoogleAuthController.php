<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    //
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }

    public function callbackGoogle(){
        try {
            $google_user = Socialite::driver('google')->user();
            $user = User::where('google_id',$google_user->getId())->first();

            if(!$user){

                $user = User::where('email',$google_user->getEmail())->first();
                // dd($user);

                if(!$user){
                    $newuser = User::create([
                        'name' => $google_user->getName(),
                        'email' => $google_user->getEmail(),
                        'google_id' => $google_user->getId()
                    ]);
    
                    Auth::login($newuser);
                    return redirect()->route('dashboard');
                }else{
                    $user->google_id = $google_user->getId();
                    $user->save();
                    Auth::login($user);
                    return redirect()->route('dashboard');
                }
            }else{
                Auth::login($user);
                return redirect()->route('dashboard');
            }
        } catch (\Throwable $th) {
            return $th;
            // return redirect()->route('login')->withErrors('An error occurred during authentication.');
        }
    }
}

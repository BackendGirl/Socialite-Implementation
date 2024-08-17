<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GitAuthController extends Controller
{
    //
    public function redirect(){
        return Socialite::driver('github')->redirect();
    }

    public function callbackgit(){
        try {
            $git_user = Socialite::driver('github')->user();
            $user = User::where('github_id',$git_user->getId())->first();

            if(!$user){

                $user = User::where('email',$git_user->getEmail())->first();
                // dd($user);

                if(!$user){
                    $newuser = User::create([
                        'name' => $git_user->getName(),
                        'email' => $git_user->getEmail(),
                        'github_id' => $git_user->getId()
                    ]);
    
                    Auth::login($newuser);
                    return redirect()->route('dashboard');
                }else{
                    $user->github_id = $git_user->getId();
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

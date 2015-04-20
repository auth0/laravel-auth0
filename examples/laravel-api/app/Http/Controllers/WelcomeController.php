<?php namespace App\Http\Controllers;

class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
//		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
    {
        $isLoggedIn = \Auth::check();
		return view('welcome.index')->with('isLoggedIn', $isLoggedIn);
	}

	public function login() {
        $auth0Config = config('laravel-auth0');
		return view('welcome.login')->with('auth0Config',$auth0Config);
	}

	public function spa() {
		return view('welcome.spa');
	}

	public function logout() {
        \Auth::logout();
        return  \Redirect::intended('/');
	}

    public function dump() {
        dd(\Auth::user()->getUserInfo());
    }

    public function api() {
        return response()->json(['status' => 'pong!']);
    }


}

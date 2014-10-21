<?php

class AuthController extends BaseController {

	/**
     * Displays the login form
     *
     * @return  Illuminate\Http\Response
     */
	public function login()
	{
		if (Confide::user()) {
           return Redirect::to('/');
			
			
        } else {
            return View::make('auth.login');
        }
	}

	/**
     * Attempt to do login
     *
     * @return  Illuminate\Http\Response
     */
	public function doLogin()
	{
		$repo = App::make('UserRepository');
        $input = Input::all();

        if ($repo->login($input)) {
			return Response::json(array('redirect' => URL::to('/')), 200);
        } else {
            if ($repo->isThrottled($input)) {
                $errorMessage = Lang::get('confide::confide.alerts.too_many_attempts');
            } elseif ($repo->existsButNotConfirmed($input)) {
                $errorMessage = Lang::get('confide::confide.alerts.not_confirmed');
            } else {
                $errorMessage = Lang::get('confide::confide.alerts.wrong_credentials');
            }

			Helpers::setExceptionErrorMessage($errorMessage);
			App::abort(403);
        }
	}

	/**
     * Log the user out of the application.
     *
     * @return  Illuminate\Http\Response
     */
    public function logout()
    {
        Confide::logout();

        return Redirect::to('/');
    }
}
<?php

class UsersController extends BaseController {

    /**
	 * Display a listing of the resource.
	 * GET /overviews
	 *
	 * @return Response
	 */
	public function index()
	{
		return View::make('users.index');
	}

    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
        // $repo = App::make('UserRepository');
        // $user = $repo->signup(Input::all());

        // if ($user->id) {
            // if (Config::get('confide::signup_email')) {
                // Mail::queueOn(
                    // Config::get('confide::email_queue'),
                    // Config::get('confide::email_account_confirmation'),
                    // compact('user'),
                    // function ($message) use ($user) {
                        // $message
                            // ->to($user->email, $user->username)
                            // ->subject(Lang::get('confide::confide.email.account_confirmation.subject'));
                    // }
                // );
            // }

            // return Redirect::action('UsersController@login')
                // ->with('notice', Lang::get('confide::confide.alerts.account_created'));
        // } else {
            // $error = $user->errors()->all(':message');

            // return Redirect::action('UsersController@create')
                // ->withInput(Input::except('password'))
                // ->with('error', $error);
        // }
    }

    
}

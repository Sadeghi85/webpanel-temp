<?php

class UsersController extends BaseController {

	public function sites($userId)
	{
		if (Request::ajax()) {
			
			return Response::json(User::getSiteData($userId));
		}
		
	}
	
	
    /**
	 * Display a listing of the resource.
	 * GET /overviews
	 *
	 * @return Response
	 */
	public function index()
	{
		if (Request::ajax())
		{
			return Response::json(User::getIndexData());
		}
		
		$roles = DB::table('roles')->lists('name', 'id');
		$roles[0] = '';
		ksort($roles);
		
		return View::make('users.index', compact('roles'));
	}

    /**
     * Stores new account
     *
     * @return  Illuminate\Http\Response
     */
    public function store()
    {
		$userInstance = new User;
		
		if ($userInstance->validationFails()) {
			Helpers::setExceptionErrorMessage($userInstance->getValidationMessage());
			App::abort(403);
		}
		
		if ( ! User::addUser()) {
			Helpers::setExceptionErrorMessage('Unable to create this user.');
			App::abort(403);
		}
		
		return Response::json(array());
		
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

    /**
	 * Remove the specified resource from storage.
	 * DELETE /overviews/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($user)
	{
		if ( ! User::removeUser($user)) {
			Helpers::setExceptionErrorMessage('Unable to remove this user.');
			App::abort(403);
		}
		
		return Response::json(array());
	}
}

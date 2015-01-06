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
		if ( ! Confide::user()->ability('Administrator', '')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		$user = new User;
		
		if ($user->validationFails()) {
			Helpers::setExceptionErrorMessage($user->getValidationMessage());
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

	public function getChangeState($user)
	{
		if ( ! Confide::user()->ability('Administrator', '')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		User::changeState($user);
		
		return Redirect::route('users.index', array('page' => Input::get('page', 1), 'pageSize' => Input::get('pageSize', 10)));
	}
	
	public function destroy($user)
	{
		if ( ! Confide::user()->ability('Administrator', 'create_site')) {
			Helpers::setExceptionErrorMessage('You don\'t have permission to access this resource.');
			App::abort(403);
		}
		
		if ( ! User::removeUser($user)) {
			Helpers::setExceptionErrorMessage('Unable to remove this user.');
			App::abort(403);
		}
		
		return Response::json(array());
	}
}

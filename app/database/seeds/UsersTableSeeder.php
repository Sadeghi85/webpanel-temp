<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		DB::table('users')->delete();
		
        try
		{
		    // Create the user
			$user = Sentry::getUserProvider()->create(array(
		        'username'  => 'root',
		        'password'  => 'root',
				'activated' => 1,
		    ));

		    // Find the group using the group name
		    $rootGroup = Sentry::getGroupProvider()->findByName('root');

		    // Assign the group to the user
		    $user->addGroup($rootGroup);
		}
		catch (Cartalyst\Sentry\Users\LoginRequiredException $e)
		{
		    echo 'Login field is required.';
		}
		catch (Cartalyst\Sentry\Users\PasswordRequiredException $e)
		{
		    echo 'Password field is required.';
		}
		catch (Cartalyst\Sentry\Users\UserExistsException $e)
		{
		    echo 'User with this login already exists.';
		}
		catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e)
		{
		    echo 'Group was not found.';
		}
	}

}
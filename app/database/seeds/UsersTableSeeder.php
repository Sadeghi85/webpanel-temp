<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		$user = new User;
		$user->username = 'administrator';
		$user->email = 'administrator@localhost.localdomain';
		$user->password = 'WebPanel';
		$user->password_confirmation = 'WebPanel';
		
		OS::addUser($user->username, $user->password);
		
		$user->save();
		$user->roles()->sync(array(Role::where('name', '=', 'Administrator')->first()->id));
	}
}
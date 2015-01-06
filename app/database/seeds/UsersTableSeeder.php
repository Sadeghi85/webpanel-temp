<?php

class UsersTableSeeder extends Seeder {

	public function run()
	{
		$user = new User;
		$user->username = 'administrator';
		$user->activated = 1;
		$user->email = 'administrator@localhost.localdomain';
		$user->password = 'WebPanel';
		$user->password_confirmation = 'WebPanel';
		
		OS::addUser($user->username, Config::get('panel.web_base_dir'), 'apache', 'WebPanel user', '/sbin/nologin', 0, $user->password)
		
		$user->save();
		$user->roles()->sync(array(Role::where('name', '=', 'Administrator')->first()->id));
	}
}
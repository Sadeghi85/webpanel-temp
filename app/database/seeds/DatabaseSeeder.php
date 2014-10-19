<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();
		
		DB::table('users')->delete();
		DB::table('roles')->delete();
		DB::table('permissions')->delete();
		DB::table('permission_role')->delete();
		DB::table('assigned_roles')->delete();

		$this->call('PermissionsTableSeeder');
		$this->call('RolesTableSeeder');
		$this->call('UsersTableSeeder');

	}

}

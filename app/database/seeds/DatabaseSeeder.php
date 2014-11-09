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
		

		
		DB::table('permission_role')->delete();
		DB::table('assigned_roles')->delete();
		DB::table('site_user')->delete();
		DB::table('site_aliases')->delete();
		
		DB::table('roles')->delete();
		DB::table('permissions')->delete();
		DB::table('users')->delete();
		DB::table('sites')->delete();

		$this->call('PermissionsTableSeeder');
		$this->call('RolesTableSeeder');
		$this->call('UsersTableSeeder');
		$this->call('SitesTableSeeder');

	}

}

<?php

class RolesTableSeeder extends Seeder {

	public function run()
	{
		$role = new Role;
		$role->name = 'Administrator';
		
		$role->save();
		
		// attaching permissions
		$role->perms()->sync(Permission::lists('id'));
	}
}
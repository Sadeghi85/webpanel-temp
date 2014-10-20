<?php

class RolesTableSeeder extends Seeder {

	public function run()
	{
		# Administrator
		$role = new Role;
		$role->name = 'Administrator';
		$role->save();
		$role->perms()->sync(Permission::lists('id'));
		
		# Staff
		$role = new Role;
		$role->name = 'Staff';
		$role->save();
		$role->perms()->sync(array(
			Permission::where('name', '=', 'create_site')->first()->id,
			Permission::where('name', '=', 'edit_site')->first()->id,
			Permission::where('name', '=', 'remove_site')->first()->id,
		));
	}
}
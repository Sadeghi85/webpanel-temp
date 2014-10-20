<?php

class PermissionsTableSeeder extends Seeder {

	public function run()
	{
		$permissions = array(
			new Permission(array('name' => 'manage_user' , 'display_name' => 'Manage User')),
			new Permission(array('name' => 'create_site' , 'display_name' => 'Create Site')),
			new Permission(array('name' => 'edit_site'   , 'display_name' => 'Edit Site'  )),
			new Permission(array('name' => 'remove_site' , 'display_name' => 'Remove Site')),
		);
		
		foreach ($permissions as $permission) {
			$permission->save();
		}
		
	}
}
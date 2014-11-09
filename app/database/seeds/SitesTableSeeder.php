<?php

class SitesTableSeeder extends Seeder {

	public function run()
	{
		$site = new Site;
		$site->tag = 'web001';
		$site->activated = 1;
		
		$site->save();
		
		
		
		$aliases = array(
			new Alias(array(
				'alias' => 'tv1.ir',
				'port' => 80,
				'server_name' => 1,
				)
			),
			new Alias(array(
				'alias' => 'ch1.iribtv.ir',
				'port' => 80,
				'server_name' => 0,
				)
			),
			new Alias(array(
				'alias' => 'ch1.iribtv.ir',
				'port' => 8080,
				'server_name' => 0,
				)
			)
		);

		$site->aliases()->saveMany($aliases);

	}
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sites', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('tag')->unique();
			$table->boolean('activated')->default(0);
			
			//$table->integer('quota')->default(0);
			//$table->string('ftp_password')->nullable();
			//$table->string('mysql_password')->nullable();
			
			$table->timestamps();
		});
		
		Schema::create('site_user', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->integer('site_id')->unsigned();
			$table->integer('user_id')->unsigned();
			
			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			
			$table->index('site_id');
			$table->index('user_id');
			
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
			$table->timestamps();
		});
		
		Schema::create('site_aliases', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->integer('site_id')->unsigned();
			$table->string('alias');
			$table->integer('port')->unsigned();
			$table->boolean('server_name')->default(false);
			
			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			
			$table->index('site_id');
			$table->unique(array('alias', 'port'));
			
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('site_aliases');
		Schema::drop('site_user');
		Schema::drop('sites');
	}

}

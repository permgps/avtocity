<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone',15)->unique();
            $table->string('name');
            $table->smallInteger('role')->default(5);
            $table->string('password');
            $table->smallInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        $user = User::create([
            'phone' => '79991112233',
            'name' => 'Администратор',
            'password' => bcrypt('password')
        ]);
        $user->role = 1;
        $user->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

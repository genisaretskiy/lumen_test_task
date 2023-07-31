<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        for ($counter = 1; $counter < 20; $counter++) {
            $id = DB::table('company')->insertGetId([
                    'title' => 'first_name' . $counter,
                    'description' => 'last_name' . $counter,
                    'phone' => 'email' . $counter,
                    'created_at' => 'NOW()',
                    'updated_at' => 'NOW()',
                ]
            );
            $usersList = [1, 2, 3, 4, 5];
            for ($index = 0; $index < random_int(1, 4); $index++) {
                $userId = random_int(0, count($usersList) - 1);
                unset($usersList[$userId]);
                DB::table('company_user')->insert([
                        'company_id' => $id,
                        'user_id' => $userId,
                        'created_at' => 'NOW()',
                        'updated_at' => 'NOW()',
                    ]
                );
            }

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};

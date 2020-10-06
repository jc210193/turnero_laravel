<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('address', 45);
            $table->string('phone', 15)->nullable();
            $table->string('menu_channel', 45);
            $table->string('panel_channel', 45);
            $table->string('office_key', 15);

            $table->foreignId('municipality_id')->constrained('municipalities');

            $table->boolean('is_active', true);

            $table->timestamps();
        });

        DB::statement("INSERT INTO  offices
            (
                id, name, address, phone, menu_channel, panel_channel, office_key, municipality_id, is_active, created_at, updated_at
            )
            VALUES
                ( 1, 'Madero Centro', 'Calle uno cruce con dos', '993128484', 'centro-0129-as', 'panel-12', '12345', 1, true, NOW(), NOW()),
                ( 2, 'Madero Sur', 'Calle uno cruce con dos', '993128484', 'centro-0129sur', 'panel-76', '54321', 1, true, NOW(), NOW()),
                ( 3, 'Madero Norte', 'Calle uno cruce con dos', '993128484', 'centro-0129-nt', 'panel-32', '11110', 1, true, NOW(), NOW())
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offices');
    }
}

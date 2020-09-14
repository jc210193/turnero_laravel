<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialityTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_types', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('description', 100)->nullable();
            $table->string('class_icon', 40);
            $table->boolean('is_active', true);
            $table->timestamps();
        });

        DB::statement("INSERT INTO  speciality_types
            (
                id, name, description, class_icon, is_active, created_at, updated_at
            )
            VALUES
                ( 1, 'Electrico', '', 'fas fa-charging-station fa-3x', 1, NOW(), NOW()),
                ( 2, 'Suspención y frenos', '', 'fas fa-tachometer-alt fa-3x', 1, NOW(), NOW()),
                ( 3, 'Clutch', '', 'fas fa-car fa-3x', 1, NOW(), NOW()),
                ( 4, 'Afinación', '', 'fas fa-tools fa-3x', 1, NOW(), NOW()),
                ( 5, 'Alineación', '', 'fas fa-car-crash fa-3x', 1, NOW(), NOW()),
                ( 6, 'Llantas', '', 'fas fa-life-ring fa-3x', 1, NOW(), NOW()),
                ( 7, 'Fuel injection', '', 'fas fa-oil-can fa-3x', 1, NOW(), NOW()),
                ( 8, 'Baterias', '', 'fas fa-car-battery fa-3x', 1, NOW(), NOW())
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('speciality_types');
    }
}

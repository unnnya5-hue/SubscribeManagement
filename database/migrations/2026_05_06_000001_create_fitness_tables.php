<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('preset_machines', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('location', 20)->default('gym');
            $table->string('category', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('preset_machine_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preset_machine_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('body_part', 50)->nullable();
            $table->decimal('met_value', 4, 1)->default(5.0);
            $table->timestamps();
        });

        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('location', 20);
            $table->string('category', 50)->nullable();
            $table->text('notes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('machine_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('body_part', 50)->nullable();
            $table->decimal('met_value', 4, 1)->default(5.0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_usage_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('sets')->default(3);
            $table->unsignedInteger('reps')->default(10);
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->timestamps();
        });

        Schema::create('schedule_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('repeat_type', 20)->default('weekly');
            $table->date('starts_on')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('schedule_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_pattern_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weekday');
            $table->foreignId('menu_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->unique(['schedule_pattern_id', 'weekday']);
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->nullable()->constrained()->nullOnDelete();
            $table->date('performed_on');
            $table->string('status', 20)->default('completed');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('workout_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_usage_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('set_number');
            $table->decimal('weight_kg', 5, 1)->nullable();
            $table->unsignedInteger('reps')->nullable();
            $table->decimal('rpe', 3, 1)->nullable();
            $table->unsignedInteger('rest_seconds')->nullable();
            $table->decimal('estimated_1rm', 6, 1)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('body_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('recorded_date');
            $table->decimal('weight_kg', 4, 1);
            $table->decimal('body_fat_pct', 3, 1)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'recorded_date']);
        });
    }

    public function down(): void
    {
        foreach ([
            'body_records',
            'workout_sets',
            'workouts',
            'schedule_days',
            'schedule_patterns',
            'menu_items',
            'menus',
            'machine_usages',
            'machines',
            'preset_machine_usages',
            'preset_machines',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

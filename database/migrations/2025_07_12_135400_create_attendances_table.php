<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
             $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('barcode_id')->nullable()->constrained('barcodes')->onDelete('set null');
            $table->date('date');
            $table->time('time_in')->nullable();  // absen masuk
            $table->time('time_out')->nullable(); // absen pulang
            $table->enum('status', ['present', 'late', 'excused', 'sick', 'absent'])->default('absent');
            $table->string('note')->nullable();
            $table->string('attachment')->nullable(); // file izin, surat dokter, dsb
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

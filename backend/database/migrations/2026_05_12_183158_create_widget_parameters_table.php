<?php

use App\Models\Widget;
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
        Schema::create('widget_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Widget::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['string', 'integer']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_parameters');
    }
};
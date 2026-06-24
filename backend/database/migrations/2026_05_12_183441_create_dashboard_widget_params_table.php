<?php
use App\Models\DashboardWidget;
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
        Schema::create('dashboard_widget_params', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(DashboardWidget::class)->constrained()->cascadeOnDelete();
            $table->string('param_name');
            $table->string('param_value');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_widget_params');
    }
};
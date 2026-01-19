<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'support', 'client'])->default('client')->after('email');
            }
        });

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country_code');
            $table->timestamps();
        });

        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('location_id')->constrained();
            $table->string('ip_address');
            $table->integer('total_ram_mb');
            $table->integer('total_cpu_cores');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('server_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->integer('ram_mb');
            $table->integer('cpu_cores');
            $table->timestamps();
        });

        Schema::create('operating_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version');
            $table->timestamps();
        });

        Schema::create('operating_system_server_plan', function (Blueprint $table) {
            $table->foreignId('server_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('operating_system_id')->constrained()->onDelete('cascade');
            $table->primary(['server_plan_id', 'operating_system_id'], 'plan_os_pk');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('server_plan_id')->constrained();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('status', ['active', 'expired', 'cancelled']);
            $table->timestamps();
        });

        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('hostname');
            $table->foreignId('user_id')->constrained();
            $table->foreignId('subscription_id')->constrained();
            $table->foreignId('node_id')->nullable()->constrained(); 
            $table->foreignId('operating_system_id')->constrained();
            $table->string('ip_address')->nullable();
            $table->string('root_password')->nullable();
            $table->enum('status', ['provisioning', 'running', 'stopped', 'suspended'])->default('provisioning');
            $table->timestamps();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('server_id')->nullable()->constrained();
            $table->string('subject');
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->enum('status', ['open', 'answered', 'closed']);
            $table->timestamps();
        });

        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained();
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained();
            $table->string('action');
            $table->text('details')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('structure_tables');
    }
};

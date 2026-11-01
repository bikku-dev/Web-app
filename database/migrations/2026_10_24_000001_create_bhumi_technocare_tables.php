<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Areas
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Cities (Linked to Area)
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Organizations / Firms (B2B Clients)
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode')->nullable();
            $table->string('gst_no')->nullable();
            $table->string('photo')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 4. Customers / Contacts
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('firm_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->string('contact_no');
            $table->string('department')->nullable();
            $table->text('address')->nullable();
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 5. Product Types (Machinery Categories)
        Schema::create('product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Service Types
        Schema::create('service_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 7. Complaints (Service Tickets)
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('firm_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('product_type_id')->constrained('product_types')->onDelete('cascade');
            $table->foreignId('service_type_id')->constrained('service_types')->onDelete('cascade');
            $table->foreignId('engineer_id')->constrained('users')->onDelete('cascade');
            $table->text('issue_description')->nullable();
            $table->dateTime('due_date');
            $table->string('photo')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_started')->default(false);
            $table->dateTime('started_at')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->dateTime('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->unsignedBigInteger('bill_id')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_deleted')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        // 8. Bills / Invoices
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('bill_number')->unique();
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->foreignId('firm_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('bill_type', ['cash', 'credit', 'bill'])->default('cash');
            $table->enum('payment_status', ['pending', 'collected', 'handed_over'])->default('pending');
            $table->boolean('is_handed')->default(false);
            $table->dateTime('handed_over_at')->nullable();
            $table->foreignId('handed_over_to')->nullable()->constrained('users')->nullOnDelete();
            $table->text('handover_remarks')->nullable();
            $table->string('gst_bill_number')->nullable();
            $table->string('photo')->nullable();
            $table->string('asset_photo')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // Add foreign key constraint to complaints.bill_id
        Schema::table('complaints', function (Blueprint $table) {
            $table->foreign('bill_id')->references('id')->on('bills')->nullOnDelete();
        });

        // 9. Credit Collections
        Schema::create('credit_collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_id')->constrained('bills')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->foreignId('collected_by')->constrained('users')->onDelete('cascade');
            $table->dateTime('collected_at');
            $table->text('notes')->nullable();
            $table->string('receipt_photo')->nullable();
            $table->timestamps();
        });

        // 10. Expenses
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->nullable()->constrained('complaints')->nullOnDelete();
            $table->foreignId('firm_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('expense_type', ['Travel', 'Component', 'Other'])->default('Travel');
            $table->decimal('amount', 12, 2);
            $table->text('details');
            $table->string('photo')->nullable();
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->timestamps();
        });

        // 11. Attendances
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('photo');
            $table->boolean('is_clock_in')->default(true);
            $table->boolean('is_approved')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approved_at')->nullable();
            $table->text('admin_remarks')->nullable();
            $table->dateTime('recorded_at');
            $table->timestamps();
        });

        // 12. Notice Board
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('notice');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->date('expires_at')->nullable();
            $table->timestamps();
        });

        // 13. Audit Logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('module');
            $table->string('reference_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('notices');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('credit_collections');

        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['bill_id']);
        });

        Schema::dropIfExists('bills');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('service_types');
        Schema::dropIfExists('product_types');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('organizations');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('areas');
    }
};

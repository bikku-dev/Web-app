<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add 'code' column if it doesn't exist
        if (!Schema::hasColumn('product_types', 'code')) {
            Schema::table('product_types', function (Blueprint $table) {
                $table->string('code', 10)->nullable()->after('name');
            });
        }

        // 2. Set up the 4 required standard product types
        $types = [
            ['name' => 'Computer', 'code' => 'C', 'description' => 'Desktops, Laptops, Servers & IT Peripherals'],
            ['name' => 'Printer',  'code' => 'P', 'description' => 'Printers, Scanners, Toners & Copiers'],
            ['name' => 'CCTV',     'code' => 'S', 'description' => 'Security Cameras, DVR, NVR & Surveillance'],
            ['name' => 'Other',    'code' => 'O', 'description' => 'Other Office Equipment & General Breakdown'],
        ];

        // Ensure these 4 exist and have correct code
        foreach ($types as $t) {
            $existing = DB::table('product_types')->where('name', $t['name'])->first();
            if ($existing) {
                DB::table('product_types')->where('id', $existing->id)->update([
                    'code' => $t['code'],
                    'description' => $t['description'],
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('product_types')->insert([
                    'name' => $t['name'],
                    'code' => $t['code'],
                    'description' => $t['description'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Get the IDs of the 4 standard types
        $computerId = DB::table('product_types')->where('name', 'Computer')->value('id');
        $printerId  = DB::table('product_types')->where('name', 'Printer')->value('id');
        $cctvId     = DB::table('product_types')->where('name', 'CCTV')->value('id');
        $otherId    = DB::table('product_types')->where('name', 'Other')->value('id');

        // Remap any complaints pointing to old product types
        $validIds = [$computerId, $printerId, $cctvId, $otherId];
        DB::table('complaints')->whereNotIn('product_type_id', $validIds)->update([
            'product_type_id' => $otherId
        ]);

        // Deactivate or remove old types that are not in the 4
        DB::table('product_types')->whereNotIn('id', $validIds)->delete();
    }

    public function down(): void
    {
        // Keep as is
    }
};

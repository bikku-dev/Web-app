<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Area;
use App\Models\City;
use App\Models\Organization;
use App\Models\Customer;
use App\Models\ProductType;
use App\Models\ServiceType;
use App\Models\Complaint;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\Notice;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users (Admin + Engineers)
        $admin = User::firstOrCreate(
            ['phone_no' => '9999999999'],
            [
                'name' => 'Bhumi Admin (Operations Head)',
                'country_code' => '+91',
                'email' => 'admin@bhumitechnocare.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department' => 'Management',
                'post' => 'Operations Director',
                'address' => 'Bhumi Technocare HQ, Ring Road, Surat',
                'blood_group' => 'O+',
                'photo' => 'images/profiles/admin.jpg',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        $engineer1 = User::firstOrCreate(
            ['phone_no' => '9876543210'],
            [
                'name' => 'Ramesh Patel',
                'country_code' => '+91',
                'email' => 'ramesh@bhumitechnocare.com',
                'password' => Hash::make('password123'),
                'role' => 'engineer',
                'department' => 'Heavy CNC & Automation',
                'post' => 'Senior Field Engineer',
                'address' => 'Plot 14, Varachha Main Road, Surat',
                'blood_group' => 'B+',
                'dob' => '1992-06-15',
                'family_contact_no' => '9825123456',
                'photo' => 'images/profiles/ramesh-patel.jpg',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        $engineer2 = User::firstOrCreate(
            ['phone_no' => '9825098250'],
            [
                'name' => 'Vikram Sharma',
                'country_code' => '+91',
                'email' => 'vikram@bhumitechnocare.com',
                'password' => Hash::make('password123'),
                'role' => 'engineer',
                'department' => 'Textile Machinery & Looms',
                'post' => 'Field Service Specialist',
                'address' => 'B-302, Diamond Residency, Surat',
                'blood_group' => 'A+',
                'dob' => '1995-11-20',
                'family_contact_no' => '9879988776',
                'photo' => 'images/profiles/vikram-sharma.jpg',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        $engineer3 = User::firstOrCreate(
            ['phone_no' => '9898098980'],
            [
                'name' => 'Ankit Verma',
                'country_code' => '+91',
                'email' => 'ankit@bhumitechnocare.com',
                'password' => Hash::make('password123'),
                'role' => 'engineer',
                'department' => 'Electrical & PLC Calibration',
                'post' => 'Junior Service Engineer',
                'address' => 'Shop 12, GIDC Housing, Surat',
                'blood_group' => 'AB+',
                'dob' => '1998-03-10',
                'family_contact_no' => '9824112233',
                'photo' => 'images/profiles/ankit-verma.jpg',
                'is_verified' => true,
                'is_active' => true,
            ]
        );

        // 2. Areas & Cities
        $areasData = [
            'GIDC Industrial Estate' => ['Surat GIDC Zone 1', 'Surat GIDC Zone 2'],
            'Ring Road Commercial Hub' => ['Ring Road Central', 'Majura Gate'],
            'Varachha Textile Zone' => ['Varachha East', 'Mini Bazar'],
            'Adajan Tech Park' => ['Adajan West', 'Pal Commercial Area'],
        ];

        $createdAreas = [];
        $createdCities = [];

        foreach ($areasData as $areaName => $cityList) {
            $area = Area::firstOrCreate(['name' => $areaName], ['is_active' => true]);
            $createdAreas[] = $area;

            foreach ($cityList as $cityName) {
                $city = City::firstOrCreate(
                    ['name' => $cityName, 'area_id' => $area->id],
                    ['is_active' => true]
                );
                $createdCities[] = $city;
            }
        }

        // 3. Product Types
        $products = [
            'CNC Milling Machine' => 'High precision multi-axis CNC milling centers',
            'Laser Cutting Machine' => 'Fiber and CO2 industrial laser sheet metal cutters',
            'Industrial Air Compressor' => 'Screw and reciprocating rotary compressors',
            'Textile Air-Jet Loom' => 'High speed electronic weaving looms',
            'High Voltage Transformer' => 'Industrial step-down transformers & switchgears',
        ];
        $createdProducts = [];
        foreach ($products as $name => $desc) {
            $createdProducts[] = ProductType::firstOrCreate(['name' => $name], ['description' => $desc, 'is_active' => true]);
        }

        // 4. Service Types
        $services = [
            'Emergency Breakdown Service' => 'Immediate on-site response for operational line halts',
            'Routine Maintenance & Inspection' => 'Scheduled periodic checkup, lubrication, and oil change',
            'Sensor & PLC Calibration' => 'Fine tuning electronics, optical sensors, and logic controllers',
            'Parts Overhaul & Replacement' => 'Replacing worn bearings, spindles, seals, and drives',
            'Installation & Commissioning' => 'New machinery assembly, leveling, alignment, and testing',
        ];
        $createdServices = [];
        foreach ($services as $name => $desc) {
            $createdServices[] = ServiceType::firstOrCreate(['name' => $name], ['description' => $desc, 'is_active' => true]);
        }

        // 5. Organizations (Firms)
        $firmsData = [
            [
                'name' => 'Shree Ram Textiles Pvt Ltd',
                'area_id' => $createdAreas[0]->id,
                'city_id' => $createdCities[0]->id,
                'contact_person' => 'Manoj Agarwal',
                'contact_no' => '9825011223',
                'address' => 'Plot 42, Sector 3, GIDC Industrial Estate',
                'pincode' => '395006',
                'gst_no' => '24AAACB1234F1Z5',
                'remarks' => 'VIP client with 24 air-jet looms and priority AMC',
            ],
            [
                'name' => 'Krishna Diamond Works',
                'area_id' => $createdAreas[1]->id,
                'city_id' => $createdCities[2]->id,
                'contact_person' => 'Dinesh Patel',
                'contact_no' => '9879022334',
                'address' => '101, Diamond Tower, Ring Road',
                'pincode' => '395002',
                'gst_no' => '24AADCK9876E1Z2',
                'remarks' => 'Laser cutting machine maintenance AMC quarterly contract',
            ],
            [
                'name' => 'Bhumi Engineering Works',
                'area_id' => $createdAreas[2]->id,
                'city_id' => $createdCities[4]->id,
                'contact_person' => 'Rajesh Sharma',
                'contact_no' => '9898033445',
                'address' => 'Shop 15, Near Flyover, Varachha Main Road',
                'pincode' => '395006',
                'gst_no' => '24AABCB5555G1Z9',
                'remarks' => 'Heavy CNC lathe & milling services workshop',
            ],
            [
                'name' => 'Apex Pharma Machinery',
                'area_id' => $createdAreas[3]->id,
                'city_id' => $createdCities[6]->id,
                'contact_person' => 'Kavita Shah',
                'contact_no' => '9824044556',
                'address' => 'Plot 88, Adajan Tech Park',
                'pincode' => '395009',
                'gst_no' => '24AAACP4444H1Z1',
                'remarks' => 'Quarterly AMC contract for high-speed packaging compressors',
            ],
        ];

        $createdFirms = [];
        foreach ($firmsData as $fd) {
            $createdFirms[] = Organization::firstOrCreate(['name' => $fd['name']], $fd);
        }

        // 6. Customers
        $customersData = [
            [
                'firm_id' => $createdFirms[0]->id,
                'name' => 'Manoj Agarwal',
                'contact_no' => '9825011223',
                'department' => 'Weaving & Production',
                'address' => 'Plot 42, GIDC Industrial Estate',
                'area_id' => $createdAreas[0]->id,
                'city_id' => $createdCities[0]->id,
                'remarks' => 'Head of Production Dept',
            ],
            [
                'firm_id' => $createdFirms[1]->id,
                'name' => 'Dinesh Patel',
                'contact_no' => '9879022334',
                'department' => 'Laser Processing Unit',
                'address' => '101, Diamond Tower, Ring Road',
                'area_id' => $createdAreas[1]->id,
                'city_id' => $createdCities[2]->id,
                'remarks' => 'Technical Operations Manager',
            ],
            [
                'firm_id' => $createdFirms[2]->id,
                'name' => 'Rajesh Sharma',
                'contact_no' => '9898033445',
                'department' => 'Heavy Engineering',
                'address' => 'Shop 15, Varachha Main Road',
                'area_id' => $createdAreas[2]->id,
                'city_id' => $createdCities[4]->id,
                'remarks' => 'Workshop Supervisor',
            ],
            [
                'firm_id' => $createdFirms[3]->id,
                'name' => 'Kavita Shah',
                'contact_no' => '9824044556',
                'department' => 'QA & Automation',
                'address' => 'Plot 88, Adajan Tech Park',
                'area_id' => $createdAreas[3]->id,
                'city_id' => $createdCities[6]->id,
                'remarks' => 'Plant QA Head',
            ],
            [
                'firm_id' => null,
                'name' => 'Sanjay Mehta',
                'contact_no' => '9876500112',
                'department' => 'Individual Proprietor',
                'address' => '55, Katargam Industrial Lane, Surat',
                'area_id' => $createdAreas[2]->id,
                'city_id' => $createdCities[4]->id,
                'remarks' => 'Direct client for motor repairs & electric drives',
            ],
        ];

        $createdCustomers = [];
        foreach ($customersData as $cd) {
            $createdCustomers[] = Customer::firstOrCreate(['contact_no' => $cd['contact_no']], $cd);
        }

        // 7. Complaints
        $complaint1 = Complaint::firstOrCreate(
            ['ticket_number' => 'CMP-2026-0001'],
            [
                'firm_id' => $createdFirms[0]->id,
                'customer_id' => $createdCustomers[0]->id,
                'product_type_id' => $createdProducts[3]->id, // Loom
                'service_type_id' => $createdServices[0]->id, // Breakdown
                'engineer_id' => $engineer2->id,
                'issue_description' => 'Main yarn feeding sensor failure causing emergency stop in loom line 3.',
                'due_date' => Carbon::now()->addDays(1)->setTime(18, 0),
                'status' => 'in_progress',
                'priority' => 'urgent',
                'is_started' => true,
                'started_at' => Carbon::now()->subHours(2),
                'created_by' => $admin->id,
            ]
        );

        $complaint2 = Complaint::firstOrCreate(
            ['ticket_number' => 'CMP-2026-0002'],
            [
                'firm_id' => $createdFirms[1]->id,
                'customer_id' => $createdCustomers[1]->id,
                'product_type_id' => $createdProducts[1]->id, // Laser cutter
                'service_type_id' => $createdServices[1]->id, // Routine Maintenance
                'engineer_id' => $engineer1->id,
                'issue_description' => 'Quarterly optic lens cleaning, coolant flush, and focal calibration.',
                'due_date' => Carbon::now()->addDays(3)->setTime(16, 0),
                'status' => 'pending',
                'priority' => 'medium',
                'is_started' => false,
                'created_by' => $admin->id,
            ]
        );

        $complaint3 = Complaint::firstOrCreate(
            ['ticket_number' => 'CMP-2026-0003'],
            [
                'firm_id' => $createdFirms[2]->id,
                'customer_id' => $createdCustomers[2]->id,
                'product_type_id' => $createdProducts[0]->id, // CNC
                'service_type_id' => $createdServices[3]->id, // Overhaul
                'engineer_id' => $engineer1->id,
                'issue_description' => 'Spindle vibration in Z-axis replaced ceramic bearings and test cut completed.',
                'due_date' => Carbon::now()->subDays(1)->setTime(14, 0),
                'status' => 'completed',
                'priority' => 'high',
                'is_started' => true,
                'started_at' => Carbon::now()->subDays(2),
                'is_resolved' => true,
                'resolved_at' => Carbon::now()->subDays(1),
                'resolution_notes' => 'Spindle disassembled, new high-speed bearings fitted, backlash calibrated to 0.002mm.',
                'created_by' => $admin->id,
            ]
        );

        // Overdue ticket for demonstration
        $complaint4 = Complaint::firstOrCreate(
            ['ticket_number' => 'CMP-2026-0004'],
            [
                'firm_id' => $createdFirms[3]->id,
                'customer_id' => $createdCustomers[3]->id,
                'product_type_id' => $createdProducts[2]->id, // Compressor
                'service_type_id' => $createdServices[0]->id, // Breakdown
                'engineer_id' => $engineer3->id,
                'issue_description' => 'Oil pressure dropping intermittently during packaging cycle.',
                'due_date' => Carbon::now()->subDays(2)->setTime(12, 0), // Overdue!
                'status' => 'pending',
                'priority' => 'urgent',
                'is_started' => false,
                'created_by' => $admin->id,
            ]
        );

        // 8. Bills
        $bill1 = Bill::firstOrCreate(
            ['bill_number' => 'BILL-2026-0101'],
            [
                'complaint_id' => $complaint3->id,
                'firm_id' => $createdFirms[2]->id,
                'customer_id' => $createdCustomers[2]->id,
                'created_by' => $engineer1->id,
                'amount' => 12500.00,
                'bill_type' => 'bill',
                'payment_status' => 'handed_over',
                'is_handed' => true,
                'handed_over_at' => Carbon::now()->subDays(1),
                'handed_over_to' => $admin->id,
                'handover_remarks' => 'Cash handed to admin in office, GST invoice issued',
                'gst_bill_number' => 'GST-INV-2026-889',
                'remarks' => 'Full spindle overhaul charges and 2x bearing set',
            ]
        );
        $complaint3->update(['bill_id' => $bill1->id]);

        $bill2 = Bill::firstOrCreate(
            ['bill_number' => 'BILL-2026-0102'],
            [
                'complaint_id' => null,
                'firm_id' => $createdFirms[0]->id,
                'customer_id' => $createdCustomers[0]->id,
                'created_by' => $engineer2->id,
                'amount' => 4500.00,
                'bill_type' => 'cash',
                'payment_status' => 'collected',
                'is_handed' => false, // Pending handover to admin
                'remarks' => 'Emergency sensor replacement charges',
            ]
        );

        $bill3 = Bill::firstOrCreate(
            ['bill_number' => 'BILL-2026-0103'],
            [
                'complaint_id' => null,
                'firm_id' => $createdFirms[1]->id,
                'customer_id' => $createdCustomers[1]->id,
                'created_by' => $engineer1->id,
                'amount' => 8000.00,
                'bill_type' => 'credit',
                'payment_status' => 'pending',
                'is_handed' => false,
                'remarks' => 'Laser nozzle consumables (Payment due within 15 days)',
            ]
        );

        // 9. Expenses
        Expense::firstOrCreate(
            ['details' => 'Fuel and toll tax for on-site visit to GIDC Estate'],
            [
                'complaint_id' => $complaint1->id,
                'firm_id' => $createdFirms[0]->id,
                'customer_id' => $createdCustomers[0]->id,
                'created_by' => $engineer2->id,
                'expense_type' => 'Travel',
                'amount' => 450.00,
                'status' => 'approved',
                'reviewed_by' => $admin->id,
                'reviewed_at' => Carbon::now()->subHours(1),
                'admin_remarks' => 'Approved as per company travel policy',
            ]
        );

        Expense::firstOrCreate(
            ['details' => 'High temperature silicone sealant and cleaner spray for CNC repair'],
            [
                'complaint_id' => $complaint3->id,
                'firm_id' => $createdFirms[2]->id,
                'customer_id' => $createdCustomers[2]->id,
                'created_by' => $engineer1->id,
                'expense_type' => 'Component',
                'amount' => 780.00,
                'status' => 'pending',
                'admin_remarks' => null,
            ]
        );

        // 10. Attendances
        Attendance::firstOrCreate(
            [
                'user_id' => $engineer1->id,
                'recorded_at' => Carbon::today()->setTime(9, 15),
                'is_clock_in' => true,
            ],
            [
                'photo' => 'uploads/attendance/sample_clock_in.jpg',
                'is_approved' => true,
                'approved_by' => $admin->id,
                'approved_at' => Carbon::today()->setTime(9, 30),
                'admin_remarks' => 'Verified on-time arrival',
            ]
        );

        Attendance::firstOrCreate(
            [
                'user_id' => $engineer2->id,
                'recorded_at' => Carbon::today()->setTime(9, 30),
                'is_clock_in' => true,
            ],
            [
                'photo' => 'uploads/attendance/sample_clock_in.jpg',
                'is_approved' => false, // Pending admin review
                'admin_remarks' => null,
            ]
        );

        // 11. Notices
        Notice::firstOrCreate(
            ['title' => 'Standard Operating Procedure (SOP) for High-Voltage Switchgear Service'],
            [
                'notice' => 'All field engineers attending electrical breakdown calls must strictly wear Class-0 insulated safety boots and lockout-tagout (LOTO) keypads before opening transformer enclosures.',
                'created_by' => $admin->id,
                'is_active' => true,
                'expires_at' => Carbon::now()->addMonths(1),
            ]
        );

        Notice::firstOrCreate(
            ['title' => 'Monthly Cash Handover & Expense Settlement Deadline'],
            [
                'notice' => 'Please submit all physical cash collections, signed customer vouchers, and spare parts receipts by the 28th of every month at the main office.',
                'created_by' => $admin->id,
                'is_active' => true,
                'expires_at' => Carbon::now()->addDays(15),
            ]
        );
    }
}

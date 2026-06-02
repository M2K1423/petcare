<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\SystemSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if they don't exist
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'Full system access'],
            ['name' => 'Veterinarian', 'slug' => 'vet', 'description' => 'Veterinary doctor'],
            ['name' => 'Receptionist', 'slug' => 'receptionist', 'description' => 'Reception staff'],
            ['name' => 'Owner', 'slug' => 'owner', 'description' => 'Pet owner/customer'],
            ['name' => 'Cashier', 'slug' => 'cashier', 'description' => 'Payment processing'],
            ['name' => 'Technician', 'slug' => 'technician', 'description' => 'Lab/Tech staff'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        // Create admin user
        $adminRole = Role::where('slug', 'admin')->first();
        User::firstOrCreate(
            ['email' => 'admin@petcare.local'],
            [
                'name' => 'Admin User',
                'email' => 'admin@petcare.local',
                'phone' => '+84123456789',
                'password' => Hash::make('password'),
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create demo users for each role
        $vetRole = Role::where('slug', 'vet')->first();
        User::firstOrCreate(
            ['email' => 'doctor1@petcare.local'],
            [
                'name' => 'Dr. Nguyễn Văn A',
                'email' => 'doctor1@petcare.local',
                'phone' => '+84987654321',
                'password' => Hash::make('password'),
                'role_id' => $vetRole->id,
                'email_verified_at' => now(),
            ]
        );

        $receptionistRole = Role::where('slug', 'receptionist')->first();
        User::firstOrCreate(
            ['email' => 'receptionist1@petcare.local'],
            [
                'name' => 'Trần Thị B',
                'email' => 'receptionist1@petcare.local',
                'phone' => '+84912345678',
                'password' => Hash::make('password'),
                'role_id' => $receptionistRole->id,
                'email_verified_at' => now(),
            ]
        );

        $ownerRole = Role::where('slug', 'owner')->first();
        User::firstOrCreate(
            ['email' => 'owner@petcare.local'],
            [
                'name' => 'Lê Văn C',
                'email' => 'owner@petcare.local',
                'phone' => '+84901234567',
                'password' => Hash::make('password'),
                'role_id' => $ownerRole->id,
                'email_verified_at' => now(),
            ]
        );

        // Create demo doctors
        $doctorUser = User::where('email', 'doctor1@petcare.local')->first();
        if ($doctorUser && !$doctorUser->doctor) {
            Doctor::firstOrCreate(
                ['license_number' => 'LIC001'],
                [
                    'user_id' => $doctorUser->id,
                    'license_number' => 'LIC001',
                    'specialty' => 'General Practice',
                    'phone' => $doctorUser->phone,
                    'email' => $doctorUser->email,
                    'years_of_experience' => 5,
                    'is_active' => true,
                ]
            );
        }

        // Create demo services
        $services = [
            [
                'name' => 'Khám Tổng Quát',
                'description' => 'Khám sức khỏe tổng quát cho thú cưng',
                'price' => 200000,
                'duration_minutes' => 30,
            ],
            [
                'name' => 'Tiêm Phòng',
                'description' => 'Tiêm phòng các loại bệnh',
                'price' => 150000,
                'duration_minutes' => 20,
            ],
            [
                'name' => 'Xét Nghiệm',
                'description' => 'Xét nghiệm máu và các chỉ số khác',
                'price' => 300000,
                'duration_minutes' => 45,
            ],
            [
                'name' => 'Spa & Grooming',
                'description' => 'Dịch vụ tắm, cắt lông và chăm sóc',
                'price' => 250000,
                'duration_minutes' => 60,
            ],
            [
                'name' => 'Phẫu Thuật',
                'description' => 'Các loại phẫu thuật nhỏ và lớn',
                'price' => 1000000,
                'duration_minutes' => 120,
            ],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(
                ['name' => $service['name']],
                $service + ['is_active' => true]
            );
        }

        // Initialize system settings
        $settings = [
            ['key' => 'clinic_name', 'value' => 'PetCare Clinic', 'data_type' => 'string', 'description' => 'Clinic name'],
            ['key' => 'clinic_phone', 'value' => '+84123456789', 'data_type' => 'string', 'description' => 'Clinic phone'],
            ['key' => 'clinic_email', 'value' => 'contact@petcare.local', 'data_type' => 'string', 'description' => 'Clinic email'],
            ['key' => 'working_hours_start', 'value' => '08:00', 'data_type' => 'string', 'description' => 'Working hours start'],
            ['key' => 'working_hours_end', 'value' => '18:00', 'data_type' => 'string', 'description' => 'Working hours end'],
            ['key' => 'appointment_slot_duration', 'value' => '30', 'data_type' => 'integer', 'description' => 'Appointment slot duration in minutes'],
            ['key' => 'appointment_advance_booking_days', 'value' => '30', 'data_type' => 'integer', 'description' => 'Days in advance for appointment booking'],
            ['key' => 'minimum_appointment_notice_hours', 'value' => '2', 'data_type' => 'integer', 'description' => 'Minimum notice before appointment in hours'],
            ['key' => 'enable_online_payment', 'value' => 'true', 'data_type' => 'boolean', 'description' => 'Enable online payment'],
            ['key' => 'deposit_policy_percentage', 'value' => '20', 'data_type' => 'integer', 'description' => 'Deposit percentage'],
            ['key' => 'enable_appointment_reminder', 'value' => 'true', 'data_type' => 'boolean', 'description' => 'Enable appointment reminders'],
            ['key' => 'reminder_hours_before', 'value' => '24', 'data_type' => 'integer', 'description' => 'Hours before appointment to send reminder'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('Admin seeder completed successfully!');
    }
}

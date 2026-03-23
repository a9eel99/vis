<?php

namespace Database\Seeders;

use App\Domain\Enums\InspectionGrade;
use App\Domain\Enums\InspectionStatus;
use App\Domain\Models\Inspection;
use App\Domain\Models\InspectionQuestion;
use App\Domain\Models\InspectionResult;
use App\Domain\Models\InspectionSection;
use App\Domain\Models\InspectionTemplate;
use App\Domain\Models\User;
use App\Domain\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Users
        $superAdmin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@vis.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $superAdmin->assignRole('Super Admin');

        $admin = User::create([
            'name' => 'Manager',
            'email' => 'manager@vis.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $admin->assignRole('Admin');

        $inspector1 = User::create([
            'name' => 'John Inspector',
            'email' => 'john@vis.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $inspector1->assignRole('Inspector');

        $inspector2 = User::create([
            'name' => 'Sarah Inspector',
            'email' => 'sarah@vis.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $inspector2->assignRole('Inspector');

        $viewer = User::create([
            'name' => 'Viewer Account',
            'email' => 'viewer@vis.local',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);
        $viewer->assignRole('Viewer');

        // Create Vehicles
        $vehicles = [];
        $vehicleData = [
            ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2023, 'color' => 'White', 'license_plate' => 'ABC-1234', 'mileage' => 15000, 'fuel_type' => 'gasoline', 'owner_name' => 'Ahmad Ali'],
            ['make' => 'Honda', 'model' => 'Civic', 'year' => 2022, 'color' => 'Black', 'license_plate' => 'DEF-5678', 'mileage' => 28000, 'fuel_type' => 'gasoline', 'owner_name' => 'Sara Hassan'],
            ['make' => 'Ford', 'model' => 'F-150', 'year' => 2024, 'color' => 'Blue', 'license_plate' => 'GHI-9012', 'mileage' => 5000, 'fuel_type' => 'diesel', 'owner_name' => 'Mohammed Khaled'],
            ['make' => 'BMW', 'model' => 'X5', 'year' => 2023, 'color' => 'Silver', 'license_plate' => 'JKL-3456', 'mileage' => 12000, 'fuel_type' => 'gasoline', 'owner_name' => 'Layla Omar'],
            ['make' => 'Mercedes', 'model' => 'C-Class', 'year' => 2021, 'color' => 'Gray', 'license_plate' => 'MNO-7890', 'mileage' => 42000, 'fuel_type' => 'diesel', 'owner_name' => 'Rami Nasser'],
        ];

        foreach ($vehicleData as $vd) {
            $vehicles[] = Vehicle::create(array_merge($vd, ['created_by' => $superAdmin->id]));
        }

        // Create Inspection Templates
        // 1. General Template (no fuel_type - works for all)
        $template = InspectionTemplate::create([
            'name' => 'Standard Vehicle Inspection',
            'description' => 'Comprehensive multi-point vehicle inspection covering exterior, interior, engine, and safety.',
            'is_active' => true,
            'fuel_type' => null,
            'created_by' => $superAdmin->id,
        ]);

        // 2. Gasoline Template
        $gasolineTemplate = InspectionTemplate::create([
            'name' => 'Gasoline Vehicle Inspection',
            'description' => 'Inspection template specifically designed for gasoline-powered vehicles.',
            'is_active' => true,
            'fuel_type' => 'gasoline',
            'created_by' => $superAdmin->id,
        ]);

        // 3. Diesel Template
        $dieselTemplate = InspectionTemplate::create([
            'name' => 'Diesel Vehicle Inspection',
            'description' => 'Inspection template specifically designed for diesel-powered vehicles.',
            'is_active' => true,
            'fuel_type' => 'diesel',
            'created_by' => $superAdmin->id,
        ]);

        // 4. Electric Template
        $electricTemplate = InspectionTemplate::create([
            'name' => 'Electric Vehicle Inspection',
            'description' => 'Inspection template for electric vehicles - battery, motor, charging system.',
            'is_active' => true,
            'fuel_type' => 'electric',
            'created_by' => $superAdmin->id,
        ]);

        // 5. Hybrid Template
        $hybridTemplate = InspectionTemplate::create([
            'name' => 'Hybrid Vehicle Inspection',
            'description' => 'Inspection template for hybrid vehicles combining engine and electric checks.',
            'is_active' => true,
            'fuel_type' => 'hybrid',
            'created_by' => $superAdmin->id,
        ]);

        // Add sections and questions to all templates
        $allTemplates = [$template, $gasolineTemplate, $dieselTemplate, $electricTemplate, $hybridTemplate];

        // Exterior Section
        $exterior = InspectionSection::create(['template_id' => $template->id, 'name' => 'Exterior Inspection', 'description' => 'Check all external components', 'sort_order' => 1]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'Body Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 2.0, 'max_score' => 10, 'is_critical' => false, 'sort_order' => 1]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'Paint Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 1.5, 'max_score' => 10, 'sort_order' => 2]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'Tire Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 2.0, 'max_score' => 10, 'is_critical' => true, 'sort_order' => 3]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'Lights Working', 'type' => 'checkbox', 'weight' => 1.5, 'max_score' => 10, 'is_critical' => true, 'sort_order' => 4]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'Exterior Photos', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'is_required' => false, 'sort_order' => 5]);

        // Interior Section
        $interior = InspectionSection::create(['template_id' => $template->id, 'name' => 'Interior Inspection', 'description' => 'Inspect cabin, seats, and dashboard', 'sort_order' => 2]);
        InspectionQuestion::create(['section_id' => $interior->id, 'label' => 'Seat Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 1.0, 'max_score' => 10, 'sort_order' => 1]);
        InspectionQuestion::create(['section_id' => $interior->id, 'label' => 'Dashboard Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 1.0, 'max_score' => 10, 'sort_order' => 2]);
        InspectionQuestion::create(['section_id' => $interior->id, 'label' => 'AC Working', 'type' => 'checkbox', 'weight' => 1.5, 'max_score' => 10, 'sort_order' => 3]);
        InspectionQuestion::create(['section_id' => $interior->id, 'label' => 'Interior Notes', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'is_required' => false, 'sort_order' => 4]);

        // Engine Section
        $engine = InspectionSection::create(['template_id' => $template->id, 'name' => 'Engine & Mechanical', 'description' => 'Engine and mechanical components check', 'sort_order' => 3]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'Engine Sound', 'type' => 'dropdown', 'options' => [['label'=>'Normal','score'=>10],['label'=>'Minor noise','score'=>6],['label'=>'Abnormal','score'=>2]], 'weight' => 2.5, 'max_score' => 10, 'is_critical' => true, 'sort_order' => 1]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'Oil Level', 'type' => 'dropdown', 'options' => [['label'=>'Normal','score'=>10],['label'=>'Low','score'=>4],['label'=>'Critical','score'=>0]], 'weight' => 2.0, 'max_score' => 10, 'is_critical' => true, 'sort_order' => 2]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'Brake Condition', 'type' => 'dropdown', 'options' => [['label'=>'Excellent','score'=>10],['label'=>'Good','score'=>7],['label'=>'Fair','score'=>4],['label'=>'Poor','score'=>1]], 'weight' => 3.0, 'max_score' => 10, 'is_critical' => true, 'sort_order' => 3]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'Mileage Reading', 'type' => 'number', 'weight' => 0, 'max_score' => 0, 'is_required' => true, 'sort_order' => 4]);

        // Create demo inspections
        $grades = [InspectionGrade::EXCELLENT, InspectionGrade::GOOD, InspectionGrade::NEEDS_ATTENTION, InspectionGrade::EXCELLENT, InspectionGrade::GOOD];
        $scores = [95.5, 82.3, 61.0, 92.1, 78.5];

        foreach ($vehicles as $i => $vehicle) {
            Inspection::create([
                'reference_number' => Inspection::generateReferenceNumber(),
                'vehicle_id' => $vehicle->id,
                'template_id' => $template->id,
                'inspector_id' => $i % 2 === 0 ? $inspector1->id : $inspector2->id,
                'created_by' => $superAdmin->id,
                'status' => InspectionStatus::COMPLETED->value,
                'total_score' => $scores[$i],
                'percentage' => $scores[$i],
                'grade' => $grades[$i]->value,
                'has_critical_failure' => false,
                'started_at' => now()->subDays(rand(1, 30)),
                'completed_at' => now()->subDays(rand(0, 29)),
            ]);
        }

        // Create additional past inspections for chart data
        for ($m = 1; $m <= 6; $m++) {
            $count = rand(3, 8);
            for ($j = 0; $j < $count; $j++) {
                $score = rand(45, 98);
                $grade = match(true) {
                    $score >= 90 => InspectionGrade::EXCELLENT,
                    $score >= 75 => InspectionGrade::GOOD,
                    $score >= 50 => InspectionGrade::NEEDS_ATTENTION,
                    default => InspectionGrade::CRITICAL,
                };

                Inspection::create([
                    'reference_number' => Inspection::generateReferenceNumber(),
                    'vehicle_id' => $vehicles[array_rand($vehicles)]->id,
                    'template_id' => $template->id,
                    'inspector_id' => rand(0,1) ? $inspector1->id : $inspector2->id,
                    'created_by' => $superAdmin->id,
                    'status' => InspectionStatus::COMPLETED->value,
                    'total_score' => $score,
                    'percentage' => $score,
                    'grade' => $grade->value,
                    'has_critical_failure' => $score < 50,
                    'started_at' => now()->subMonths($m)->addDays(rand(0, 25)),
                    'completed_at' => now()->subMonths($m)->addDays(rand(0, 27)),
                    'created_at' => now()->subMonths($m)->addDays(rand(0, 25)),
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarMakesAndColorsSeeder extends Seeder
{
    public function run(): void
    {
        $makes = [
            ['name_en' => 'Toyota', 'name_ar' => 'تويوتا', 'models' => ['Camry','Corolla','RAV4','Land Cruiser','Prado','Hilux','Yaris','Avalon','C-HR','Fortuner','Highlander','4Runner','Supra','GR86','Crown','Tundra','Tacoma','Sequoia','bZ4X']],
            ['name_en' => 'Hyundai', 'name_ar' => 'هيونداي', 'models' => ['Tucson','Elantra','Sonata','Santa Fe','Accent','Creta','Kona','Palisade','Venue','i10','i20','i30','Ioniq 5','Ioniq 6','Staria','Azera']],
            ['name_en' => 'Kia', 'name_ar' => 'كيا', 'models' => ['Sportage','K5','Cerato','Sorento','Seltos','Picanto','Soul','Carnival','Stinger','EV6','Niro','Telluride','Rio','Forte']],
            ['name_en' => 'Honda', 'name_ar' => 'هوندا', 'models' => ['Civic','Accord','CR-V','HR-V','Pilot','City','Jazz','Odyssey','Passport','Ridgeline']],
            ['name_en' => 'Nissan', 'name_ar' => 'نيسان', 'models' => ['Patrol','X-Trail','Qashqai','Altima','Sentra','Kicks','Pathfinder','Sunny','Maxima','Juke','Navara','Leaf','Ariya','Z']],
            ['name_en' => 'BMW', 'name_ar' => 'بي إم دبليو', 'models' => ['3 Series','5 Series','7 Series','X1','X3','X5','X7','X6','4 Series','2 Series','iX','i4','i7','M3','M4','M5']],
            ['name_en' => 'Mercedes-Benz', 'name_ar' => 'مرسيدس', 'models' => ['C-Class','E-Class','S-Class','A-Class','CLA','GLA','GLC','GLE','GLS','G-Class','AMG GT','EQS','EQE','EQB']],
            ['name_en' => 'Audi', 'name_ar' => 'أودي', 'models' => ['A3','A4','A6','A8','Q3','Q5','Q7','Q8','e-tron','RS3','RS5','S4','S6','TT']],
            ['name_en' => 'Volkswagen', 'name_ar' => 'فولكسفاغن', 'models' => ['Golf','Passat','Tiguan','Touareg','Jetta','Polo','T-Cross','T-Roc','Atlas','Arteon','ID.4']],
            ['name_en' => 'Ford', 'name_ar' => 'فورد', 'models' => ['F-150','Mustang','Explorer','Escape','Edge','Bronco','Ranger','Expedition','EcoSport','Maverick','Taurus']],
            ['name_en' => 'Chevrolet', 'name_ar' => 'شيفروليه', 'models' => ['Silverado','Tahoe','Suburban','Traverse','Equinox','Blazer','Malibu','Camaro','Trax','Trailblazer','Colorado']],
            ['name_en' => 'Jeep', 'name_ar' => 'جيب', 'models' => ['Wrangler','Grand Cherokee','Cherokee','Compass','Renegade','Gladiator','Commander','Avenger']],
            ['name_en' => 'Dodge', 'name_ar' => 'دودج', 'models' => ['Charger','Challenger','Durango','RAM 1500','RAM 2500','Hornet']],
            ['name_en' => 'Lexus', 'name_ar' => 'لكزس', 'models' => ['ES','IS','LS','RX','NX','UX','GX','LX','LC','RC','RZ']],
            ['name_en' => 'Land Rover', 'name_ar' => 'لاند روفر', 'models' => ['Range Rover','Range Rover Sport','Range Rover Velar','Range Rover Evoque','Defender','Discovery','Discovery Sport']],
            ['name_en' => 'Porsche', 'name_ar' => 'بورشه', 'models' => ['Cayenne','Macan','Panamera','911','Taycan','Cayman','Boxster']],
            ['name_en' => 'Mitsubishi', 'name_ar' => 'ميتسوبيشي', 'models' => ['Outlander','ASX','L200','Pajero','Eclipse Cross','Xpander','Lancer','Attrage']],
            ['name_en' => 'Mazda', 'name_ar' => 'مازدا', 'models' => ['3','6','CX-3','CX-5','CX-9','CX-30','CX-50','CX-60','MX-5']],
            ['name_en' => 'Subaru', 'name_ar' => 'سوبارو', 'models' => ['Outback','Forester','Crosstrek','Impreza','WRX','Legacy','BRZ','Solterra','Ascent']],
            ['name_en' => 'Suzuki', 'name_ar' => 'سوزوكي', 'models' => ['Vitara','Swift','Jimny','S-Cross','Baleno','Dzire','Ertiga','Celerio']],
            ['name_en' => 'Peugeot', 'name_ar' => 'بيجو', 'models' => ['208','308','408','508','2008','3008','5008','Partner']],
            ['name_en' => 'Renault', 'name_ar' => 'رينو', 'models' => ['Duster','Megane','Clio','Captur','Koleos','Kadjar','Arkana']],
            ['name_en' => 'Chery', 'name_ar' => 'شيري', 'models' => ['Tiggo 4','Tiggo 7','Tiggo 8','Arrizo 5','Arrizo 6','Omoda 5']],
            ['name_en' => 'Geely', 'name_ar' => 'جيلي', 'models' => ['Emgrand','Coolray','Azkarra','Monjaro','Starray','Geometry C']],
            ['name_en' => 'Changan', 'name_ar' => 'شانجان', 'models' => ['CS35','CS55','CS75','CS85','CS95','Alsvin','Eado','UNI-T','UNI-K']],
            ['name_en' => 'MG', 'name_ar' => 'إم جي', 'models' => ['ZS','HS','RX5','5','6','GT','Marvel R','MG4']],
            ['name_en' => 'BYD', 'name_ar' => 'بي واي دي', 'models' => ['Atto 3','Tang','Song Plus','Han','Seal','Dolphin','Seagull']],
            ['name_en' => 'Volvo', 'name_ar' => 'فولفو', 'models' => ['XC40','XC60','XC90','S60','S90','V60','C40']],
            ['name_en' => 'Infiniti', 'name_ar' => 'إنفينيتي', 'models' => ['Q50','Q60','QX50','QX55','QX60','QX80']],
            ['name_en' => 'Genesis', 'name_ar' => 'جينيسيس', 'models' => ['G70','G80','G90','GV60','GV70','GV80']],
            ['name_en' => 'GMC', 'name_ar' => 'جي إم سي', 'models' => ['Sierra','Yukon','Terrain','Acadia','Canyon','Hummer EV']],
            ['name_en' => 'Cadillac', 'name_ar' => 'كاديلاك', 'models' => ['Escalade','CT4','CT5','XT4','XT5','XT6','Lyriq']],
            ['name_en' => 'Isuzu', 'name_ar' => 'إيسوزو', 'models' => ['D-Max','MU-X']],
            ['name_en' => 'JAC', 'name_ar' => 'جاك', 'models' => ['S2','S3','S4','S7','J7','T6','T8']],
            ['name_en' => 'Haval', 'name_ar' => 'هافال', 'models' => ['H6','Jolion','Dargo','H9']],
            ['name_en' => 'Tesla', 'name_ar' => 'تسلا', 'models' => ['Model 3','Model Y','Model S','Model X','Cybertruck']],
        ];

        foreach ($makes as $i => $make) {
            DB::table('car_makes')->updateOrInsert(
                ['name_en' => $make['name_en']],
                [
                    'name_ar' => $make['name_ar'],
                    'models' => json_encode($make['models']),
                    'is_active' => true,
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $colors = [
            ['name_en' => 'White', 'name_ar' => 'أبيض', 'hex' => '#FFFFFF'],
            ['name_en' => 'Black', 'name_ar' => 'أسود', 'hex' => '#000000'],
            ['name_en' => 'Silver', 'name_ar' => 'فضي', 'hex' => '#C0C0C0'],
            ['name_en' => 'Gray', 'name_ar' => 'رمادي', 'hex' => '#808080'],
            ['name_en' => 'Dark Gray', 'name_ar' => 'رمادي غامق', 'hex' => '#404040'],
            ['name_en' => 'Red', 'name_ar' => 'أحمر', 'hex' => '#DC2626'],
            ['name_en' => 'Dark Red', 'name_ar' => 'خمري', 'hex' => '#7F1D1D'],
            ['name_en' => 'Blue', 'name_ar' => 'أزرق', 'hex' => '#2563EB'],
            ['name_en' => 'Dark Blue', 'name_ar' => 'كحلي', 'hex' => '#1E3A5F'],
            ['name_en' => 'Light Blue', 'name_ar' => 'أزرق فاتح', 'hex' => '#60A5FA'],
            ['name_en' => 'Green', 'name_ar' => 'أخضر', 'hex' => '#16A34A'],
            ['name_en' => 'Dark Green', 'name_ar' => 'أخضر غامق', 'hex' => '#14532D'],
            ['name_en' => 'Beige', 'name_ar' => 'بيج', 'hex' => '#D2B48C'],
            ['name_en' => 'Brown', 'name_ar' => 'بني', 'hex' => '#78350F'],
            ['name_en' => 'Gold', 'name_ar' => 'ذهبي', 'hex' => '#D4A017'],
            ['name_en' => 'Champagne', 'name_ar' => 'شامبين', 'hex' => '#F7E7CE'],
            ['name_en' => 'Orange', 'name_ar' => 'برتقالي', 'hex' => '#EA580C'],
            ['name_en' => 'Yellow', 'name_ar' => 'أصفر', 'hex' => '#EAB308'],
            ['name_en' => 'Purple', 'name_ar' => 'بنفسجي', 'hex' => '#7C3AED'],
            ['name_en' => 'Pink', 'name_ar' => 'زهري', 'hex' => '#EC4899'],
            ['name_en' => 'Burgundy', 'name_ar' => 'فيراني', 'hex' => '#800020'],
            ['name_en' => 'Bronze', 'name_ar' => 'برونزي', 'hex' => '#CD7F32'],
            ['name_en' => 'Pearl White', 'name_ar' => 'أبيض لؤلؤي', 'hex' => '#F5F5F0'],
            ['name_en' => 'Matte Black', 'name_ar' => 'أسود مطفي', 'hex' => '#1A1A1A'],
        ];

        foreach ($colors as $i => $color) {
            DB::table('car_colors')->updateOrInsert(
                ['name_en' => $color['name_en']],
                [
                    'name_ar' => $color['name_ar'],
                    'hex' => $color['hex'],
                    'is_active' => true,
                    'sort_order' => $i,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Car makes (' . count($makes) . ') and colors (' . count($colors) . ') seeded.');
    }
}
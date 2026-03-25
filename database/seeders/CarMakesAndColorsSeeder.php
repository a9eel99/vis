<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarMakesAndColorsSeeder extends Seeder
{
    public function run(): void
    {
        $makes = [
            // ═══ Top sellers in Jordan ═══
            ['name_en' => 'Toyota', 'name_ar' => 'تويوتا', 'models' => ['Camry','Corolla','RAV4','Land Cruiser','Land Cruiser Pickup','Prado','Hilux','Yaris','Avalon','C-HR','Fortuner','Highlander','4Runner','Supra','GR86','Crown','Tundra','Tacoma','Sequoia','bZ4X','Rush','Avanza','Prius','FJ Cruiser','Land Cruiser 70']],
            ['name_en' => 'Hyundai', 'name_ar' => 'هيونداي', 'models' => ['Tucson','Elantra','Sonata','Santa Fe','Accent','Creta','Kona','Palisade','Venue','i10','i20','i30','Ioniq 5','Ioniq 6','Staria','Azera','Avante','H100','Ioniq','Veloster','Grand i10','Verna']],
            ['name_en' => 'Kia', 'name_ar' => 'كيا', 'models' => ['Sportage','K5','Cerato','Sorento','Seltos','Picanto','Soul','Carnival','Stinger','EV6','Niro','Telluride','Rio','Forte','K3','K8','Frontier','Sephia','Spectra','Optima','Pegas','Sonet','Mohave']],
            ['name_en' => 'Changan', 'name_ar' => 'شانجان', 'models' => ['E-Star','EADO','EADO EV','CS35','CS55','CS75','CS85','CS95','Alsvin','UNI-T','UNI-K','UNI-V','SL03','Deepal S05','Deepal S07','Deepal L07','Oshan X5','Oshan X7']],
            ['name_en' => 'BYD', 'name_ar' => 'بي واي دي', 'models' => ['Atto 3','Tang','Song Plus','Song Pro','Han','Seal','Dolphin','Seagull','Destroyer 05','Qin Plus','Yuan Plus','F3','Shark','Sea Lion']],
            ['name_en' => 'MG', 'name_ar' => 'إم جي', 'models' => ['ZS','ZS EV','HS','RX5','5','6','GT','Marvel R','MG4','MG7','Whale','RX8']],

            // ═══ Japanese ═══
            ['name_en' => 'Nissan', 'name_ar' => 'نيسان', 'models' => ['Patrol','X-Trail','Qashqai','Altima','Sentra','Kicks','Pathfinder','Sunny','Maxima','Juke','Navara','Leaf','Ariya','Z','Tiida','Versa','Murano','Rogue','Frontier','Xterra']],
            ['name_en' => 'Honda', 'name_ar' => 'هوندا', 'models' => ['Civic','Accord','CR-V','HR-V','Pilot','City','Jazz','Odyssey','Passport','Ridgeline','Fit','Insight','ZR-V']],
            ['name_en' => 'Mitsubishi', 'name_ar' => 'ميتسوبيشي', 'models' => ['Outlander','ASX','L200','Pajero','Pajero Sport','Eclipse Cross','Xpander','Lancer','Attrage','Mirage','Montero']],
            ['name_en' => 'Mazda', 'name_ar' => 'مازدا', 'models' => ['3','6','CX-3','CX-5','CX-9','CX-30','CX-50','CX-60','MX-5','2','CX-90']],
            ['name_en' => 'Suzuki', 'name_ar' => 'سوزوكي', 'models' => ['Vitara','Swift','Jimny','S-Cross','Baleno','Dzire','Ertiga','Celerio','Alto','Ciaz','APV','Fronx','Grand Vitara']],
            ['name_en' => 'Subaru', 'name_ar' => 'سوبارو', 'models' => ['Outback','Forester','Crosstrek','Impreza','WRX','Legacy','BRZ','Solterra','Ascent','XV']],
            ['name_en' => 'Lexus', 'name_ar' => 'لكزس', 'models' => ['ES','IS','LS','RX','NX','UX','GX','LX','LC','RC','RZ','TX']],
            ['name_en' => 'Isuzu', 'name_ar' => 'إيسوزو', 'models' => ['D-Max','MU-X','N-Series','F-Series','ELF']],
            ['name_en' => 'Daihatsu', 'name_ar' => 'دايهاتسو', 'models' => ['Terios','Sirion','Charade','Mira','Move','Rocky']],
            ['name_en' => 'Infiniti', 'name_ar' => 'إنفينيتي', 'models' => ['Q50','Q60','QX50','QX55','QX60','QX80']],

            // ═══ German ═══
            ['name_en' => 'BMW', 'name_ar' => 'بي إم دبليو', 'models' => ['3 Series','5 Series','7 Series','X1','X2','X3','X4','X5','X6','X7','4 Series','2 Series','1 Series','iX','i4','i5','i7','M2','M3','M4','M5','Z4']],
            ['name_en' => 'Mercedes-Benz', 'name_ar' => 'مرسيدس', 'models' => ['C-Class','E-Class','S-Class','A-Class','B-Class','CLA','CLS','GLA','GLB','GLC','GLE','GLS','G-Class','AMG GT','EQS','EQE','EQB','EQA','Sprinter','V-Class','Vito']],
            ['name_en' => 'Volkswagen', 'name_ar' => 'فولكسفاغن', 'models' => ['Golf','Passat','Tiguan','Touareg','Jetta','Polo','T-Cross','T-Roc','Atlas','Arteon','ID.4','ID.3','ID. Buzz','Caddy','Transporter']],
            ['name_en' => 'Audi', 'name_ar' => 'أودي', 'models' => ['A3','A4','A5','A6','A7','A8','Q2','Q3','Q5','Q7','Q8','e-tron','RS3','RS5','RS7','S4','S5','S6','TT','R8']],
            ['name_en' => 'Porsche', 'name_ar' => 'بورشه', 'models' => ['Cayenne','Macan','Panamera','911','Taycan','Cayman','Boxster','718']],
            ['name_en' => 'Opel', 'name_ar' => 'أوبل', 'models' => ['Corsa','Astra','Insignia','Mokka','Grandland','Crossland']],
            ['name_en' => 'MINI', 'name_ar' => 'ميني', 'models' => ['Cooper','Clubman','Countryman','Paceman']],
            ['name_en' => 'Skoda', 'name_ar' => 'سكودا', 'models' => ['Octavia','Superb','Kodiaq','Karoq','Kamiq','Fabia','Scala']],

            // ═══ American ═══
            ['name_en' => 'Ford', 'name_ar' => 'فورد', 'models' => ['F-150','Mustang','Explorer','Escape','Edge','Bronco','Ranger','Expedition','EcoSport','Maverick','Taurus','Fusion','Focus','Raptor','Flex']],
            ['name_en' => 'Chevrolet', 'name_ar' => 'شيفروليه', 'models' => ['Silverado','Tahoe','Suburban','Traverse','Equinox','Blazer','Malibu','Camaro','Trax','Trailblazer','Colorado','Captiva','Spark','Cruze','Impala']],
            ['name_en' => 'Dodge', 'name_ar' => 'دودج', 'models' => ['Charger','Challenger','Durango','RAM 1500','RAM 2500','Hornet','Journey','Neon','Nitro','Caliber']],
            ['name_en' => 'Jeep', 'name_ar' => 'جيب', 'models' => ['Wrangler','Grand Cherokee','Cherokee','Compass','Renegade','Gladiator','Commander','Avenger','Liberty']],
            ['name_en' => 'GMC', 'name_ar' => 'جي إم سي', 'models' => ['Sierra','Yukon','Terrain','Acadia','Canyon','Hummer EV','Envoy','Savana']],
            ['name_en' => 'Cadillac', 'name_ar' => 'كاديلاك', 'models' => ['Escalade','CT4','CT5','XT4','XT5','XT6','Lyriq','CTS','ATS','SRX']],
            ['name_en' => 'Chrysler', 'name_ar' => 'كرايسلر', 'models' => ['300','Pacifica','Voyager','Town & Country']],
            ['name_en' => 'Lincoln', 'name_ar' => 'لينكولن', 'models' => ['Navigator','Aviator','Corsair','Nautilus','MKZ','MKC','Continental']],
            ['name_en' => 'Tesla', 'name_ar' => 'تسلا', 'models' => ['Model 3','Model Y','Model S','Model X','Cybertruck']],
            ['name_en' => 'Hummer', 'name_ar' => 'هامر', 'models' => ['H2','H3','EV']],
            ['name_en' => 'Buick', 'name_ar' => 'بيوك', 'models' => ['Envision','Enclave','Encore','LaCrosse']],

            // ═══ Korean ═══
            ['name_en' => 'Genesis', 'name_ar' => 'جينيسيس', 'models' => ['G70','G80','G90','GV60','GV70','GV80']],
            ['name_en' => 'Daewoo', 'name_ar' => 'دايو', 'models' => ['Lanos','Nubira','Lacetti','Matiz','Cielo']],
            ['name_en' => 'SsangYong', 'name_ar' => 'سانغ يونغ', 'models' => ['Tivoli','Rexton','Korando','Musso','Actyon']],
            ['name_en' => 'Samsung', 'name_ar' => 'سامسونغ', 'models' => ['SM3','SM5','SM6','SM7','QM3','QM5','QM6']],

            // ═══ French ═══
            ['name_en' => 'Peugeot', 'name_ar' => 'بيجو', 'models' => ['208','308','408','508','2008','3008','5008','Partner','301','Expert']],
            ['name_en' => 'Renault', 'name_ar' => 'رينو', 'models' => ['Duster','Megane','Clio','Captur','Koleos','Kadjar','Arkana','Logan','Symbol','Fluence']],
            ['name_en' => 'Citroen', 'name_ar' => 'ستروين', 'models' => ['C3','C4','C5','C5 Aircross','Berlingo','C-Elysee']],

            // ═══ Chinese ═══
            ['name_en' => 'Geely', 'name_ar' => 'جيلي', 'models' => ['Emgrand','Coolray','Azkarra','Monjaro','Starray','Geometry C','Tugella','Preface','Atlas']],
            ['name_en' => 'Chery', 'name_ar' => 'شيري', 'models' => ['Tiggo 4','Tiggo 4 Pro','Tiggo 7','Tiggo 7 Pro','Tiggo 8','Tiggo 8 Pro','Arrizo 5','Arrizo 6','Omoda 5','Omoda 7','Jaecoo J7']],
            ['name_en' => 'Jetour', 'name_ar' => 'جيتور', 'models' => ['X70','X70 Plus','X90','T2','Dashing']],
            ['name_en' => 'Haval', 'name_ar' => 'هافال', 'models' => ['H6','Jolion','Dargo','H9','Big Dog']],
            ['name_en' => 'Great Wall', 'name_ar' => 'قريت وول', 'models' => ['Wingle','Poer','Tank 300','Tank 500']],
            ['name_en' => 'Dongfeng', 'name_ar' => 'دونغ فينغ', 'models' => ['AX7','Rich','S50','Glory 580','Aeolus']],
            ['name_en' => 'JAC', 'name_ar' => 'جاك', 'models' => ['S2','S3','S4','S7','J7','T6','T8','iEV']],
            ['name_en' => 'Neta', 'name_ar' => 'نيتا', 'models' => ['V','U','S','GT','X','L']],
            ['name_en' => 'Hongqi', 'name_ar' => 'هونشي', 'models' => ['H5','H9','HS5','HS7','E-HS9','E-QM5']],
            ['name_en' => 'BAIC', 'name_ar' => 'بايك', 'models' => ['X55','X7','BJ40','EU5','D20']],
            ['name_en' => 'Bestune', 'name_ar' => 'بيستون', 'models' => ['T77','T99','B70','NAT']],
            ['name_en' => 'GAC', 'name_ar' => 'جي أي سي', 'models' => ['GS3','GS4','GS5','GS8','Emkoo','Aion S','Aion Y']],
            ['name_en' => 'Wuling', 'name_ar' => 'ووليج', 'models' => ['Mini EV','Almaz','Air EV']],
            ['name_en' => 'Forthing', 'name_ar' => 'فورثينق', 'models' => ['T5 EVO','U-Tour','Friday']],
            ['name_en' => 'Maxus', 'name_ar' => 'ماكسوس', 'models' => ['T60','D90','Euniq 6','Deliver 9']],
            ['name_en' => 'Foton', 'name_ar' => 'فوتون', 'models' => ['Tunland','Toano','View']],
            ['name_en' => 'Zeekr', 'name_ar' => 'زيكر', 'models' => ['001','007','009','X']],
            ['name_en' => 'Avatr', 'name_ar' => 'أفاتر', 'models' => ['11','12']],
            ['name_en' => 'Seres', 'name_ar' => 'سيريس', 'models' => ['3','5','7']],
            ['name_en' => 'Skywell', 'name_ar' => 'سكاي ويل', 'models' => ['ET5','BE11']],
            ['name_en' => 'Leapmotor', 'name_ar' => 'ليب موتور', 'models' => ['T03','C01','C10','C11','C16']],
            ['name_en' => 'Rising', 'name_ar' => 'رايزنق', 'models' => ['F5','F7']],

            // ═══ British ═══
            ['name_en' => 'Land Rover', 'name_ar' => 'لاند روفر', 'models' => ['Range Rover','Range Rover Sport','Range Rover Velar','Range Rover Evoque','Defender','Discovery','Discovery Sport']],
            ['name_en' => 'Jaguar', 'name_ar' => 'جاكوار', 'models' => ['F-Pace','E-Pace','I-Pace','XE','XF','XJ','F-Type']],
            ['name_en' => 'Bentley', 'name_ar' => 'بنتلي', 'models' => ['Continental GT','Flying Spur','Bentayga']],
            ['name_en' => 'Rolls-Royce', 'name_ar' => 'رولز رويس', 'models' => ['Ghost','Phantom','Wraith','Dawn','Cullinan','Spectre']],

            // ═══ Swedish ═══
            ['name_en' => 'Volvo', 'name_ar' => 'فولفو', 'models' => ['XC40','XC60','XC90','S60','S90','V60','C40','EX30','EX90']],
            ['name_en' => 'Polestar', 'name_ar' => 'بولستار', 'models' => ['2','3','4']],
            ['name_en' => 'Saab', 'name_ar' => 'ساب', 'models' => ['9-3','9-5']],

            // ═══ Italian ═══
            ['name_en' => 'Fiat', 'name_ar' => 'فيات', 'models' => ['500','500X','Tipo','Panda','Punto','Doblo']],
            ['name_en' => 'Alfa Romeo', 'name_ar' => 'ألفا روميو', 'models' => ['Giulia','Stelvio','Tonale','Giulietta']],
            ['name_en' => 'Maserati', 'name_ar' => 'مازيراتي', 'models' => ['Ghibli','Levante','Quattroporte','MC20','Grecale']],

            // ═══ Other ═══
            ['name_en' => 'Lada', 'name_ar' => 'لادا', 'models' => ['Granta','Vesta','Niva','XRAY']],
            ['name_en' => 'Proton', 'name_ar' => 'بروتون', 'models' => ['Saga','X50','X70','Persona']],
            ['name_en' => 'Mahindra', 'name_ar' => 'ماهيندرا', 'models' => ['Scorpio','XUV700','Thar','Bolero']],
            ['name_en' => 'Smart', 'name_ar' => 'سمارت', 'models' => ['ForTwo','ForFour','#1','#3']],
            ['name_en' => 'Seat', 'name_ar' => 'سيات', 'models' => ['Ibiza','Leon','Arona','Ateca','Tarraco']],
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
            ['name_en' => 'Pearl White', 'name_ar' => 'أبيض لؤلؤي', 'hex' => '#F5F5F0'],
            ['name_en' => 'Black', 'name_ar' => 'أسود', 'hex' => '#000000'],
            ['name_en' => 'Matte Black', 'name_ar' => 'أسود مطفي', 'hex' => '#1A1A1A'],
            ['name_en' => 'Silver', 'name_ar' => 'فضي', 'hex' => '#C0C0C0'],
            ['name_en' => 'Gray', 'name_ar' => 'رمادي', 'hex' => '#808080'],
            ['name_en' => 'Dark Gray', 'name_ar' => 'رمادي غامق', 'hex' => '#404040'],
            ['name_en' => 'Red', 'name_ar' => 'أحمر', 'hex' => '#DC2626'],
            ['name_en' => 'Dark Red', 'name_ar' => 'خمري', 'hex' => '#7F1D1D'],
            ['name_en' => 'Burgundy', 'name_ar' => 'فيراني', 'hex' => '#800020'],
            ['name_en' => 'Blue', 'name_ar' => 'أزرق', 'hex' => '#2563EB'],
            ['name_en' => 'Dark Blue', 'name_ar' => 'كحلي', 'hex' => '#1E3A5F'],
            ['name_en' => 'Light Blue', 'name_ar' => 'أزرق فاتح', 'hex' => '#60A5FA'],
            ['name_en' => 'Green', 'name_ar' => 'أخضر', 'hex' => '#16A34A'],
            ['name_en' => 'Dark Green', 'name_ar' => 'أخضر غامق', 'hex' => '#14532D'],
            ['name_en' => 'Olive', 'name_ar' => 'زيتي', 'hex' => '#556B2F'],
            ['name_en' => 'Beige', 'name_ar' => 'بيج', 'hex' => '#D2B48C'],
            ['name_en' => 'Brown', 'name_ar' => 'بني', 'hex' => '#78350F'],
            ['name_en' => 'Gold', 'name_ar' => 'ذهبي', 'hex' => '#D4A017'],
            ['name_en' => 'Champagne', 'name_ar' => 'شامبين', 'hex' => '#F7E7CE'],
            ['name_en' => 'Orange', 'name_ar' => 'برتقالي', 'hex' => '#EA580C'],
            ['name_en' => 'Yellow', 'name_ar' => 'أصفر', 'hex' => '#EAB308'],
            ['name_en' => 'Purple', 'name_ar' => 'بنفسجي', 'hex' => '#7C3AED'],
            ['name_en' => 'Pink', 'name_ar' => 'زهري', 'hex' => '#EC4899'],
            ['name_en' => 'Bronze', 'name_ar' => 'برونزي', 'hex' => '#CD7F32'],
            ['name_en' => 'Copper', 'name_ar' => 'نحاسي', 'hex' => '#B87333'],
            ['name_en' => 'Sand', 'name_ar' => 'رملي', 'hex' => '#C2B280'],
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
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictUpazilaSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            ['name' => 'Dhaka', 'upazilas' => ['Dhaka Sadar', 'Dhamrai', 'Dohar', 'Keraniganj', 'Nawabganj', 'Savar', 'Tejgaon', 'Gulshan', 'Mirpur', 'Uttara']],
            ['name' => 'Chittagong', 'upazilas' => ['Chittagong Sadar', 'Anwara', 'Banshkhali', 'Boalkhali', 'Chandanaish', 'Fatikchhari', 'Hathazari', 'Lohagara', 'Mirsharai', 'Patia', 'Rangunia', 'Raozan', 'Sandwip', 'Satkania', 'Sitakunda']],
            ['name' => 'Rajshahi', 'upazilas' => ['Rajshahi Sadar', 'Bagha', 'Bagmara', 'Charghat', 'Durgapur', 'Godagari', 'Mohanpur', 'Paba', 'Puthia', 'Tanore']],
            ['name' => 'Khulna', 'upazilas' => ['Khulna Sadar', 'Batiaghata', 'Dacope', 'Dumuria', 'Dighalia', 'Koyra', 'Paikgachha', 'Phultala', 'Rupsa', 'Terokhada']],
            ['name' => 'Barisal', 'upazilas' => ['Barisal Sadar', 'Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gournadi', 'Hizla', 'Mehendiganj', 'Muladi', 'Wazirpur']],
            ['name' => 'Sylhet', 'upazilas' => ['Sylhet Sadar', 'Balaganj', 'Beanibazar', 'Bishwanath', 'Companiganj', 'Dakshin Surma', 'Fenchuganj', 'Golapganj', 'Gowainghat', 'Jaintiapur', 'Kanaighat', 'Osmani Nagar', 'Zakiganj']],
            ['name' => 'Rangpur', 'upazilas' => ['Rangpur Sadar', 'Badarganj', 'Gangachara', 'Kaunia', 'Mithapukur', 'Pirgachha', 'Pirganj', 'Taraganj']],
            ['name' => 'Mymensingh', 'upazilas' => ['Mymensingh Sadar', 'Bhaluka', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur', 'Haluaghat', 'Ishwarganj', 'Muktagachha', 'Nandail', 'Phulpur', 'Trishal']],
            ['name' => 'Comilla', 'upazilas' => ['Comilla Sadar', 'Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram', 'Daudkandi', 'Debidwar', 'Homna', 'Laksam', 'Meghna', 'Muradnagar', 'Nangalkot', 'Titas']],
            ['name' => 'Narayanganj', 'upazilas' => ['Narayanganj Sadar', 'Araihazar', 'Bandar', 'Fatullah', 'Rupganj', 'Sonargaon']],
            ['name' => 'Gazipur', 'upazilas' => ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur']],
            ['name' => 'Jessore', 'upazilas' => ['Jessore Sadar', 'Abhaynagar', 'Bagherpara', 'Chaugachha', 'Jhikargachha', 'Keshabpur', 'Manirampur', 'Sharsha']],
            ['name' => 'Bogra', 'upazilas' => ['Bogra Sadar', 'Adamdighi', 'Dhunat', 'Dhupchanchia', 'Gabtali', 'Kahaloo', 'Nandigram', 'Sariakandi', 'Shajahanpur', 'Sherpur', 'Shibganj', 'Sonatala']],
            ['name' => 'Dinajpur', 'upazilas' => ['Dinajpur Sadar', 'Birampur', 'Birganj', 'Birol', 'Bochaganj', 'Chirirbandar', 'Phulbari', 'Ghoraghat', 'Hakimpur', 'Kaharole', 'Khansama', 'Nawabganj', 'Parbatipur']],
            ['name' => 'Pabna', 'upazilas' => ['Pabna Sadar', 'Atgharia', 'Bera', 'Bhangura', 'Chatmohar', 'Faridpur', 'Ishwardi', 'Santhia', 'Sujanagar']],
            ['name' => 'Kushtia', 'upazilas' => ['Kushtia Sadar', 'Bheramara', 'Daulatpur', 'Khoksa', 'Kumarkhali', 'Mirpur']],
            ['name' => 'Tangail', 'upazilas' => ['Tangail Sadar', 'Basail', 'Bhuapur', 'Delduar', 'Ghatail', 'Gopalpur', 'Kalihati', 'Madhupur', 'Mirzapur', 'Nagarpur', 'Sakhipur']],
            ['name' => 'Faridpur', 'upazilas' => ['Faridpur Sadar', 'Alfadanga', 'Bhanga', 'Boalmari', 'Charbhadrasan', 'Madhukhali', 'Nagarkanda', 'Sadarpur', 'Saltha']],
            ['name' => 'Sherpur', 'upazilas' => ['Sherpur Sadar', 'Jhenaigati', 'Nakla', 'Nalitabari', 'Sreebardi']],
            ['name' => 'Jamalpur', 'upazilas' => ['Jamalpur Sadar', 'Bakshiganj', 'Dewanganj', 'Islampur', 'Madarganj', 'Melandaha', 'Sarishabari']],
            ['name' => 'Netrokona', 'upazilas' => ['Netrokona Sadar', 'Atpara', 'Barhatta', 'Durgapur', 'Kalmakanda', 'Kendua', 'Khaliajuri', 'Madan', 'Mohanganj', 'Purbadhala']],
            ['name' => 'Kishoreganj', 'upazilas' => ['Kishoreganj Sadar', 'Austagram', 'Bajitpur', 'Bhairab', 'Hossainpur', 'Itna', 'Karimganj', 'Katiadi', 'Kuliarchar', 'Mithamain', 'Nikli', 'Pakundia', 'Tarail']],
            ['name' => 'Manikganj', 'upazilas' => ['Manikganj Sadar', 'Daulatpur', 'Ghior', 'Harirampur', 'Saturia', 'Shivalaya', 'Singair']],
            ['name' => 'Munshiganj', 'upazilas' => ['Munshiganj Sadar', 'Gazaria', 'Lohajang', 'Sirajdikhan', 'Sreenagar', 'Tongibari']],
            ['name' => 'Narsingdi', 'upazilas' => ['Narsingdi Sadar', 'Belabo', 'Monohardi', 'Palash', 'Raipura', 'Shibpur']],
            ['name' => 'Gopalganj', 'upazilas' => ['Gopalganj Sadar', 'Kashiani', 'Kotalipara', 'Muksudpur', 'Tungipara']],
            ['name' => 'Madaripur', 'upazilas' => ['Madaripur Sadar', 'Kalkini', 'Rajoir', 'Shibchar']],
            ['name' => 'Shariatpur', 'upazilas' => ['Shariatpur Sadar', 'Bhedarganj', 'Damudya', 'Gosairhat', 'Naria', 'Zanjira']],
            ['name' => 'Rajbari', 'upazilas' => ['Rajbari Sadar', 'Baliakandi', 'Goalandaghat', 'Kalukhali', 'Pangsha']],
            ['name' => 'Natore', 'upazilas' => ['Natore Sadar', 'Bagatipara', 'Baraigram', 'Gurudaspur', 'Lalpur', 'Naldanga', 'Singra']],
            ['name' => 'Sirajganj', 'upazilas' => ['Sirajganj Sadar', 'Belkuchi', 'Chauhali', 'Kamarkhanda', 'Kazipur', 'Raiganj', 'Shahjadpur', 'Tarash', 'Ullahpara']],
            ['name' => 'Joypurhat', 'upazilas' => ['Joypurhat Sadar', 'Akkelpur', 'Kalai', 'Khetlal', 'Panchbibi']],
            ['name' => 'Naogaon', 'upazilas' => ['Naogaon Sadar', 'Atrai', 'Badalgachhi', 'Dhamoirhat', 'Manda', 'Mohadevpur', 'Niamatpur', 'Patnitala', 'Porsha', 'Raninagar', 'Sapahar']],
            ['name' => 'Chapainawabganj', 'upazilas' => ['Chapainawabganj Sadar', 'Bholahat', 'Gomastapur', 'Nachol', 'Shibganj']],
            ['name' => 'Bagerhat', 'upazilas' => ['Bagerhat Sadar', 'Chitalmari', 'Fakirhat', 'Kachua', 'Mollahat', 'Mongla', 'Morrelganj', 'Rampal', 'Sarankhola']],
            ['name' => 'Chuadanga', 'upazilas' => ['Chuadanga Sadar', 'Alamdanga', 'Damurhuda', 'Jibannagar']],
            ['name' => 'Jhenaidah', 'upazilas' => ['Jhenaidah Sadar', 'Harinakunda', 'Kotchandpur', 'Maheshpur', 'Kaliganj', 'Shailkupa']],
            ['name' => 'Magura', 'upazilas' => ['Magura Sadar', 'Mohammadpur', 'Shalikha', 'Sreepur']],
            ['name' => 'Meherpur', 'upazilas' => ['Meherpur Sadar', 'Gangni', 'Mujibnagar']],
            ['name' => 'Narail', 'upazilas' => ['Narail Sadar', 'Kalia', 'Lohagara']],
            ['name' => 'Satkhira', 'upazilas' => ['Satkhira Sadar', 'Assasuni', 'Debhata', 'Kalaroa', 'Kaliganj', 'Shyamnagar', 'Tala']],
            ['name' => 'Barguna', 'upazilas' => ['Barguna Sadar', 'Amtali', 'Bamna', 'Betagi', 'Patharghata', 'Taltali']],
            ['name' => 'Bhola', 'upazilas' => ['Bhola Sadar', 'Borhanuddin', 'Char Fasson', 'Daulatkhan', 'Lalmohan', 'Manpura', 'Tazumuddin']],
            ['name' => 'Jhalokathi', 'upazilas' => ['Jhalokathi Sadar', 'Kathalia', 'Nalchity', 'Rajapur']],
            ['name' => 'Patuakhali', 'upazilas' => ['Patuakhali Sadar', 'Bauphal', 'Dashmina', 'Dumki', 'Galachipa', 'Kalapara', 'Mirzaganj', 'Rangabali']],
            ['name' => 'Pirojpur', 'upazilas' => ['Pirojpur Sadar', 'Bhandaria', 'Kawkhali', 'Mathbaria', 'Nazirpur', 'Nesarabad', 'Zianagar']],
            ['name' => 'Bandarban', 'upazilas' => ['Bandarban Sadar', 'Alikadam', 'Naikhongchhari', 'Rowangchhari', 'Ruma', 'Thanchi', 'Lama']],
            ['name' => 'Brahmanbaria', 'upazilas' => ['Brahmanbaria Sadar', 'Ashuganj', 'Bancharampur', 'Bijoynagar', 'Kasba', 'Nabinagar', 'Nasirnagar', 'Sarail']],
            ['name' => 'Chandpur', 'upazilas' => ['Chandpur Sadar', 'Faridganj', 'Haimchar', 'Hajiganj', 'Kachua', 'Matlab Dakshin', 'Matlab Uttar', 'Shahrasti']],
            ['name' => 'Lakshmipur', 'upazilas' => ['Lakshmipur Sadar', 'Kamalnagar', 'Ramganj', 'Ramgati', 'Raipur', 'Lakshmipur Sadar']],
            ['name' => 'Noakhali', 'upazilas' => ['Noakhali Sadar', 'Begumganj', 'Chatkhil', 'Companiganj', 'Hatiya', 'Kabirhat', 'Senbagh', 'Sonaimuri', 'Subarnachar']],
            ['name' => 'Feni', 'upazilas' => ['Feni Sadar', 'Chhagalnaiya', 'Daganbhuiyan', 'Fulgazi', 'Parshuram', 'Sonagazi']],
            ['name' => 'Cox\'s Bazar', 'upazilas' => ['Cox\'s Bazar Sadar', 'Chakaria', 'Eidgaon', 'Kutubdia', 'Maheshkhali', 'Pekua', 'Ramu', 'Teknaf', 'Ukhia']],
            ['name' => 'Khagrachhari', 'upazilas' => ['Khagrachhari Sadar', 'Dighinala', 'Lakshmichhari', 'Mahalchhari', 'Manikchhari', 'Matiranga', 'Panchhari', 'Ramgarh']],
            ['name' => 'Rangamati', 'upazilas' => ['Rangamati Sadar', 'Bagaichhari', 'Barkal', 'Belaichhari', 'Juraichhari', 'Kaptai', 'Kawkhali', 'Langadu', 'Naniarchar', 'Rajasthali']],
            ['name' => 'Habiganj', 'upazilas' => ['Habiganj Sadar', 'Ajmiriganj', 'Bahubal', 'Baniachong', 'Chunarughat', 'Lakhai', 'Madhabpur', 'Nabiganj']],
            ['name' => 'Moulvibazar', 'upazilas' => ['Moulvibazar Sadar', 'Barlekha', 'Juri', 'Kamalganj', 'Kulaura', 'Rajnagar', 'Sreemangal']],
            ['name' => 'Sunamganj', 'upazilas' => ['Sunamganj Sadar', 'Bishwamvarpur', 'Chhatak', 'Dakshin Sunamganj', 'Derai', 'Dharamapasha', 'Dowarabazar', 'Jagannathpur', 'Jamalganj', 'Sullah', 'Tahirpur']],
            ['name' => 'Panchagarh', 'upazilas' => ['Panchagarh Sadar', 'Atwari', 'Boda', 'Debiganj', 'Tetulia']],
            ['name' => 'Thakurgaon', 'upazilas' => ['Thakurgaon Sadar', 'Baliadangi', 'Haripur', 'Pirganj', 'Ranishankail']],
            ['name' => 'Nilphamari', 'upazilas' => ['Nilphamari Sadar', 'Dimla', 'Domar', 'Jaldhaka', 'Kishoreganj', 'Saidpur']],
            ['name' => 'Lalmonirhat', 'upazilas' => ['Lalmonirhat Sadar', 'Aditmari', 'Hatibandha', 'Kaliganj', 'Patgram']],
            ['name' => 'Kurigram', 'upazilas' => ['Kurigram Sadar', 'Bhurungamari', 'Char Rajibpur', 'Chilmari', 'Phulbari', 'Nageshwari', 'Rajarhat', 'Raumari', 'Ulipur']],
            ['name' => 'Gaibandha', 'upazilas' => ['Gaibandha Sadar', 'Fulchhari', 'Gobindaganj', 'Palashbari', 'Sadullapur', 'Saghata', 'Sundarganj']],
        ];

        $inserts = [];
        foreach ($districts as $district) {
            foreach ($district['upazilas'] as $upazila) {
                $inserts[] = [
                    'district' => $district['name'],
                    'upazila' => $upazila,
                ];
            }
        }

        $chunks = array_chunk($inserts, 500);
        foreach ($chunks as $chunk) {
            DB::table('districts_upazilas')->insert($chunk);
        }

        $this->command->info('Seeded ' . count($inserts) . ' district/upazila entries across ' . count($districts) . ' districts.');
    }
}

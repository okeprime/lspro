<?php
$provincesUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json';
$provincesData = file_get_contents($provincesUrl);
$provinces = json_decode($provincesData, true);

$regenciesMap = [];

$nameMap = [
    'NANGGROE ACEH DARUSSALAM' => 'Aceh',
    'ACEH' => 'Aceh',
    'SUMATERA UTARA' => 'Sumatera Utara',
    'SUMATERA BARAT' => 'Sumatera Barat',
    'RIAU' => 'Riau',
    'JAMBI' => 'Jambi',
    'SUMATERA SELATAN' => 'Sumatera Selatan',
    'BENGKULU' => 'Bengkulu',
    'LAMPUNG' => 'Lampung',
    'KEPULAUAN BANGKA BELITUNG' => 'Kepulauan Bangka Belitung',
    'KEPULAUAN RIAU' => 'Kepulauan Riau',
    'DKI JAKARTA' => 'DKI Jakarta',
    'JAWA BARAT' => 'Jawa Barat',
    'JAWA TENGAH' => 'Jawa Tengah',
    'DI YOGYAKARTA' => 'DI Yogyakarta',
    'JAWA TIMUR' => 'Jawa Timur',
    'BANTEN' => 'Banten',
    'BALI' => 'Bali',
    'NUSA TENGGARA BARAT' => 'Nusa Tenggara Barat',
    'NUSA TENGGARA TIMUR' => 'Nusa Tenggara Timur',
    'KALIMANTAN BARAT' => 'Kalimantan Barat',
    'KALIMANTAN TENGAH' => 'Kalimantan Tengah',
    'KALIMANTAN SELATAN' => 'Kalimantan Selatan',
    'KALIMANTAN TIMUR' => 'Kalimantan Timur',
    'KALIMANTAN UTARA' => 'Kalimantan Utara',
    'SULAWESI UTARA' => 'Sulawesi Utara',
    'SULAWESI TENGAH' => 'Sulawesi Tengah',
    'SULAWESI SELATAN' => 'Sulawesi Selatan',
    'SULAWESI TENGGARA' => 'Sulawesi Tenggara',
    'GORONTALO' => 'Gorontalo',
    'SULAWESI BARAT' => 'Sulawesi Barat',
    'MALUKU' => 'Maluku',
    'MALUKU UTARA' => 'Maluku Utara',
    'PAPUA' => 'Papua',
    'PAPUA BARAT' => 'Papua Barat',
    'PAPUA SELATAN' => 'Papua Selatan',
    'PAPUA TENGAH' => 'Papua Tengah',
    'PAPUA PEGUNUNGAN' => 'Papua Pegunungan',
    'PAPUA BARAT DAYA' => 'Papua Barat Daya'
];

foreach ($provinces as $prov) {
    $mappedName = isset($nameMap[$prov['name']]) ? $nameMap[$prov['name']] : ucwords(strtolower($prov['name']));
    
    $regenciesUrl = "https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$prov['id']}.json";
    $regData = file_get_contents($regenciesUrl);
    if($regData) {
        $regs = json_decode($regData, true);
        $kotaList = [];
        foreach($regs as $r) {
            $kotaList[] = ucwords(strtolower($r['name']));
        }
        $kotaList[] = 'Lainnya';
        $regenciesMap[$mappedName] = $kotaList;
    }
}

$regenciesMap['Luar Negeri'] = ['Singapura', 'Malaysia', 'Tiongkok', 'Jepang', 'Lainnya (Internasional)'];

if (!is_dir('public/js')) {
    mkdir('public/js', 0777, true);
}

$jsContent = "const regencies = " . json_encode($regenciesMap, JSON_PRETTY_PRINT) . ";\n";
file_put_contents('public/js/daerah.js', $jsContent);
echo "Berhasil membuat public/js/daerah.js\n";
?>

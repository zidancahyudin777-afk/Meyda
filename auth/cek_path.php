<?php
echo "<h2>Diagnosis File & Folder</h2>";
echo "<b>Lokasi file ini:</b> " . __DIR__ . "<br><br>";

// 1. Cek isi folder 'auth'
echo "<b>Isi folder 'auth' (tempat file ini berada):</b><br>";
$files_auth = scandir(__DIR__);
echo "<pre>" . print_r($files_auth, true) . "</pre>";

// 2. Cek folder di atasnya (root folder project)
$parent_dir = dirname(__DIR__);
echo "<b>Isi folder project (satu level di atas):</b><br>";
echo "Path: " . $parent_dir . "<br>";
if(is_dir($parent_dir)){
    $files_parent = scandir($parent_dir);
    echo "<pre>" . print_r($files_parent, true) . "</pre>";
} else {
    echo "<span style='color:red'>Gagal membuka folder di atasnya!</span><br>";
}

// 3. Cek spesifik folder includes
$includes_path = $parent_dir . '/includes';
echo "<b>Cek Folder 'includes':</b><br>";
if(is_dir($includes_path)){
    echo "Folder 'includes' <span style='color:green'>DITEMUKAN</span>.<br>";
    echo "Isi folder 'includes':<br>";
    echo "<pre>" . print_r(scandir($includes_path), true) . "</pre>";
} else {
    echo "Folder 'includes' <span style='color:red'>TIDAK DITEMUKAN</span> di: $includes_path<br>";
    echo "Coba perhatikan nama folder di list 'Isi folder project' di atas. Apakah namanya 'Includes' (huruf besar)? atau 'include' (tanpa s)?";
}
?>

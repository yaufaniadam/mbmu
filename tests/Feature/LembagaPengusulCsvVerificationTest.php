<?php

use App\Models\LembagaPengusul;
use Illuminate\Support\Facades\DB;

test('lembaga pengusul data matches csv file', function () {
    $this->seed(\Database\Seeders\SppgExcelSeeder::class);
    
    $csvPath = database_path('seeders/data/sppg_excel_import.csv');
    
    expect(file_exists($csvPath))->toBeTrue();

    $handle = fopen($csvPath, 'r');
    
    // Handle BOM if present
    $bom = fread($handle, 3);
    if ($bom !== "\xEF\xBB\xBF") {
        rewind($handle);
    }

    $header = fgetcsv($handle, 0, ';');
    $header = array_map(fn($h) => trim(str_replace('"', '', $h)), $header);
    
    $expectedLembagas = [];

    while (($row = fgetcsv($handle, 0, ';')) !== false) {
        if (count($row) !== count($header)) {
            continue;
        }

        $data = array_combine($header, $row);
        
        $namaPengusulRaw = trim($data['pengusul'] ?? '');
        $namaSppg = trim($data['nama_sppg'] ?? '');
        
        if (empty($namaPengusulRaw) || empty($namaSppg)) {
            continue;
        }

        $namaLembaga = $namaPengusulRaw;
        $expectedLembagas[] = $namaLembaga;

        // Verify that this specific lembaga exists
        $lembaga = LembagaPengusul::where('nama_lembaga', $namaLembaga)->first();
        
        expect($lembaga)->not->toBeNull("Lembaga Pengusul not found: {$namaLembaga}");
    }

    fclose($handle);

    // Verify total count matches unique expected names
    $uniqueExpectedCount = count(array_unique($expectedLembagas));
    $actualCount = LembagaPengusul::count();
    
    expect($actualCount)->toBe($uniqueExpectedCount, "Total count of Lembaga Pengusul does not match CSV unique entries");
});

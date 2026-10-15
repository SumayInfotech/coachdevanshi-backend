<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class ImportCountryStateCity extends Command
{
    protected $signature = 'import:locations';
    protected $description = 'Import Countries, States, and Cities from CSV files';

    public function handle()
    {
        $this->importCountries();
        $this->importStates();
        $this->importCities();

        $this->info('Import completed successfully!');
    }

    private function importCountries()
    {
        // $csvFile = storage_path('app/public/location/countries.csv');
        $csvFile = public_path('assets/import-data/location/countries.csv');
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            DB::table('countries')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'name' => $row['name'],
                    'iso2' => $row['iso2'],
                    'status' => ($row['name'] === 'United States') ? 'Active' : 'Inactive',
                ]
            );
        }
        $this->info('Countries imported.');
    }

    private function importStates()
    {
        // $csvFile = storage_path('app/public/location/states.csv');
        $csvFile = public_path('assets/import-data/location/states.csv');
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            DB::table('states')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'name' => $row['name'],
                    'country_id' => $row['country_id'],
                ]
            );
        }
        $this->info('States imported.');
    }

    private function importCities()
    {
        // $csvFile = storage_path('app/public/location/cities.csv');
        $csvFile = public_path('assets/import-data/location/cities.csv');
        $csv = Reader::createFromPath($csvFile, 'r');
        $csv->setHeaderOffset(0);

        foreach ($csv as $row) {
            DB::table('cities')->updateOrInsert(
                ['id' => $row['id']],
                [
                    'name' => $row['name'],
                    'state_id' => $row['state_id'],
                ]
            );
        }
        $this->info('Cities imported.');
    }
}

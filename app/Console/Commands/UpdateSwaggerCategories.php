<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;

class UpdateSwaggerCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:update-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It update the category enum list for Swagger checkboxes';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        exec('php artisan l5-swagger:generate');
        // i take all the categpries from the db
        $categories = Category::pluck('name')->toArray();

        // i convert the array into a string formatted for Swagger
        $enumString = implode('", "', $categories);

        // i create the path to Swagger configuration file
        $swaggerFilePath = base_path('storage/api-docs/api-docs.json');

        // i read the configuration file
        $fileContents = file_get_contents($swaggerFilePath);

        // i replace ENUM_PLACEHOLDER with the categories list
        $fileContentModified = str_replace('"ENUM_PLACEHOLDER"', '"default",' . '"' . $enumString . '"', $fileContents);

        // i overwrite the file with the new categories
        $result = file_put_contents($swaggerFilePath, $fileContentModified);

        $readAgain = file_get_contents($swaggerFilePath);

        // if you want to check, uncomment this line
        // $this->info($readAgain);

        // i update the documentation
        $this->info('Swagger documentation successfully updated!');
    }
}

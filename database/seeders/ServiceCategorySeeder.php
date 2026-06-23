<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Domain', 'description' => 'Domain Registration & Renewal'],
            ['name' => 'Hosting', 'description' => 'Web Hosting, Shared, Reseller'],
            ['name' => 'Server', 'description' => 'VPS / Dedicated Servers'],
            ['name' => 'SSL', 'description' => 'SSL Certificates'],
            ['name' => 'Website', 'description' => 'Website Development services'],
            ['name' => 'Application', 'description' => 'Web & Mobile Application Development'],
            ['name' => 'Maintenance', 'description' => 'Website & Server AMC Maintenance Contracts'],
            ['name' => 'Other', 'description' => 'Custom IT Services & consultings'],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description'], 'status' => 'Active']
            );
        }
    }
}

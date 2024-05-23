<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Products;
use App\Models\SubCategory;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $userCount = User::count();

        $categoryCount = Category::count();

        $subCategoryCount = SubCategory::count();

        $productCount = Products::count();
        
        return [
            Stat::make('Total Users', $userCount)
                ->description('Number of registered users')
                ->descriptionIcon('heroicon-o-users', IconPosition::Before),
            Stat::make('Total Categories', $categoryCount)
                ->description('Number of Categories')
                ->descriptionIcon('heroicon-o-squares-plus', IconPosition::Before),
            Stat::make('Total Sub Categories', $subCategoryCount)
                ->description('Number of Sub Categories')
                ->descriptionIcon('heroicon-o-squares-plus', IconPosition::Before),
            Stat::make('Total Products', $productCount)
                ->description('Number of Products')
                ->descriptionIcon('heroicon-o-cube', IconPosition::Before),
            
        ];
    }
}

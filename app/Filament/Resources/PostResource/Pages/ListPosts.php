<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

use Filament\Schemas\Components\Tabs\Tab;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending_review' => Tab::make('Pending Review')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending_review'))
                ->badge(\App\Models\Post::where('status', 'pending_review')->count())
                ->badgeColor('info'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'published'))
                ->badgeColor('success'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'draft')),
            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'archived')),
        ];
    }
}

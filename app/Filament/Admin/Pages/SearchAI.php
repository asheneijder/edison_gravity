<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Services\SearchAiService;

class SearchAI extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';

    protected string $view = 'filament.pages.search-ai';

    protected static ?string $navigationLabel = 'Edison AI';

    protected static ?string $title = 'Search AI';

    protected static ?string $slug = 'search-ai';

    protected static ?int $navigationSort = 1;

    protected static \UnitEnum|string|null $navigationGroup = null; // Top-level item

    // Livewire Properties
    public $query = '';
    public $results = [];
    public $artbotResponse = '';

    public static function canAccess(): bool
    {
        return auth()->user()->can_view_search_ai;
    }

    public function mount()
    {
        // 
    }

    public function updatedQuery()
    {
        $this->performSearch();
    }

    public function search()
    {
        $this->performSearch();
    }

    protected function performSearch()
    {
        $this->artbotResponse = '';

        if (strlen($this->query) < 2) {
            $this->results = [];
            return;
        }

        $service = app(SearchAiService::class);
        $this->results = $service->search($this->query);
        $this->generateArtbotResponse();
    }

    protected function generateArtbotResponse()
    {
        $countFunc = count($this->results['functions'] ?? []);
        $countUsers = count($this->results['users'] ?? []);
        $countMsgs = count($this->results['messages'] ?? []);
        $countIn = count($this->results['inbound_files'] ?? []);
        $countOut = count($this->results['outbound_files'] ?? []);

        $total = $countFunc + $countUsers + $countMsgs + $countIn + $countOut;

        if ($total === 0) {
            $this->artbotResponse = "I couldn't find any resources matching '{$this->query}'. Try refining your search terms.";
            return;
        }

        $parts = [];
        if ($countFunc)
            $parts[] = "$countFunc functions";
        if ($countUsers)
            $parts[] = "$countUsers users";
        if ($countMsgs)
            $parts[] = "$countMsgs SWIFT messages";
        if ($countIn)
            $parts[] = "$countIn inbound files";
        if ($countOut)
            $parts[] = "$countOut outbound files";

        $summary = implode(', ', $parts);

        $this->artbotResponse = "I found {$summary} related to your query. ";

        if ($countOut > 0) {
            $this->artbotResponse .= " You can download the generated outbound files directly from the section below.";
        }
    }

    public function downloadFile(string $path)
    {
        if (!\Illuminate\Support\Facades\Storage::disk('swift')->exists($path)) {
            \Filament\Notifications\Notification::make()
                ->title('File not found')
                ->danger()
                ->send();
            return;
        }

        return \Illuminate\Support\Facades\Storage::disk('swift')->download($path);
    }


}

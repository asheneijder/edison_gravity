<?php

namespace App\Services;

use App\Models\SwiftMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\NavigationGroup;

class SearchAiService
{
    /**
     * Main search entry point.
     *
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        if (strlen($query) < 2) {
            return [];
        }

        return [
            'functions' => $this->searchFunctions($query),
            'messages' => $this->searchSwiftMessages($query),
            'inbound_files' => $this->searchInboundFiles($query),
            'outbound_files' => $this->searchOutboundFiles($query),
            'users' => $this->searchUsers($query),
        ];
    }

    /**
     * Search Users (from 'Functions' page data).
     */
    protected function searchUsers(string $query): array
    {
        return \App\Models\User::query()
            ->where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->limit(5)
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    // Link to the Functions page or User Resource if available
                    // Since 'Functions' page lists them, maybe link there?
                    // But 'Functions' page doesn't seem to have a detail view per user based on the code.
                    // linking to edit might be tricky without a Resource.
                    // Check if UserResource exists (it does).
                    'url' => Filament::getPanel()->getId() === 'admin'
                        ? \App\Filament\Admin\Resources\Users\UserResource::getUrl('edit', ['record' => $user])
                        : null,
                ];
            })
            ->toArray();
    }

    /**
     * Search Filament Navigation (Functions).
     */
    protected function searchFunctions(string $query): array
    {
        $results = [];
        $query = strtolower($query);

        // Get navigation from the current panel
        $navigation = Filament::getNavigation();

        foreach ($navigation as $groupOrItem) {
            if ($groupOrItem instanceof NavigationGroup) {
                foreach ($groupOrItem->getItems() as $item) {
                    if (str_contains(strtolower($item->getLabel()), $query)) {
                        $results[] = [
                            'label' => $item->getLabel(),
                            'url' => $item->getUrl(),
                            'icon' => $item->getIcon(),
                            'group' => $groupOrItem->getLabel(),
                        ];
                    }
                }
            } elseif ($groupOrItem instanceof NavigationItem) {
                if (str_contains(strtolower($groupOrItem->getLabel()), $query)) {
                    $results[] = [
                        'label' => $groupOrItem->getLabel(),
                        'url' => $groupOrItem->getUrl(),
                        'icon' => $groupOrItem->getIcon(),
                        'group' => null,
                    ];
                }
            }
        }

        // Also search Resources directly if not in nav? 
        // Filament::getNavigation() usually covers visible items.
        // We can also add "Pages" that might not be in the main nav but are registered.
        // For now, sticking to Navigation matches "Functions".

        return $results;
    }

    /**
     * Search Database Swift Messages.
     */
    protected function searchSwiftMessages(string $query): array
    {
        return SwiftMessage::query()
            ->where('frm_BIC', 'like', "%{$query}%")
            ->orWhere('to_BIC', 'like', "%{$query}%")
            ->orWhere('type', 'like', "%{$query}%")
            ->orWhere('source_file', 'like', "%{$query}%")
            // JSON search (MySQL 5.7+ / MariaDB 10.2+)
            // ->orWhereJsonContains('messages', $query) // This acts on keys/values, simple text search on JSON text might be better for "AI" feel
            ->orWhereRaw('LOWER(messages) LIKE ?', ['%' . strtolower($query) . '%'])
            ->latest('system_datime')
            ->limit(10)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'type' => $msg->type,
                    'sender' => $msg->frm_BIC,
                    'receiver' => $msg->to_BIC,
                    'file' => $msg->source_file,
                    'date' => $msg->system_datime,
                ];
            })
            ->toArray();
    }

    /**
     * Search Inbound Files (Raw).
     */
    protected function searchInboundFiles(string $query): array
    {
        return $this->searchFilesOnDisk('swift', 'inbound', $query);
    }

    /**
     * Search Outbound Files (CSV).
     */
    protected function searchOutboundFiles(string $query): array
    {
        return $this->searchFilesOnDisk('swift', 'outbound', $query);
    }

    /**
     * Helper to search files recursively on a disk.
     */
    protected function searchFilesOnDisk(string $diskName, string $directory, string $query): array
    {
        $results = [];
        $disk = Storage::disk($diskName);

        // List all files recursively
        // Warning: Performance impact if too many files.
        // For a hackathon/MVP, this is fine. For prod, we'd want indexing.
        $files = $disk->allFiles($directory);

        $query = strtolower($query);

        foreach ($files as $file) {
            $filename = basename($file);
            if (str_contains(strtolower($filename), $query)) {
                $results[] = [
                    'path' => $file,
                    'name' => $filename,
                    'size' => $disk->size($file),
                    'last_modified' => $disk->lastModified($file),
                ];
                if (count($results) >= 10)
                    break; // Limit
            }
        }

        return $results;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SwiftMessage;
use App\Services\SwiftEngineService;
use Carbon\Carbon;

class SwiftDownloadController extends Controller
{
    public function downloadCsv($date, $type)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $messages = SwiftMessage::whereDate('system_datime', $date)
            ->where('type', $type)
            ->get();

        if ($messages->isEmpty()) {
            return back()->with('error', 'No messages found.');
        }

        // Format data for CSV generator
        // SwiftEngineService expects array of arrays
        $rows = $messages->map(function ($msg) {
            return $msg->messages;
        })->filter()->toArray();

        $service = new SwiftEngineService();
        $csvContent = $service->generateCsvContent($rows);

        $filename = sprintf(
            "%s_%s.csv",
            $type,
            Carbon::parse($date)->format('Ymd')
        );

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename);
    }
}

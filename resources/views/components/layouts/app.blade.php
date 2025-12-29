<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? 'Account Setup' }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <style>
            :root {
                --primary: #6366f1; /* Indigo 500 - brighter for dark mode */
                --primary-hover: #818cf8;
                --bg-color: #111827; /* Gray 900 */
                --card-bg: #1f2937; /* Gray 800 */
                --text-main: #f9fafb; /* Gray 50 */
                --text-muted: #9ca3af; /* Gray 400 */
                --border-color: #374151; /* Gray 700 */
                --danger: #f87171; /* Red 400 */
                --success: #34d399; /* Emerald 400 */
                --input-bg: #374151; /* Gray 700 */
            }
            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--bg-color);
                color: var(--text-main);
                margin: 0;
                padding: 0;
                line-height: 1.5;
            }
            [x-cloak] { display: none !important; }
        </style>

        @filamentStyles
    </head>
    <body>
        {{ $slot }}

        @filamentScripts
    </body>
</html>

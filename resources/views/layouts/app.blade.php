<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Vazirmatn', sans-serif;
        }

        /* RTL Overrides */
        .rtl {
            direction: rtl;
        }

        /* Persian number formatting */
        .persian-numbers {
            font-feature-settings: "ss01";
        }

        /* Custom scrollbar for RTL */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Form styling for RTL */
        .form-input, .form-select, .form-textarea {
            text-align: right;
        }

        /* Button alignment */
        .btn-group {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-start;
        }

        /* Table styling for RTL */
        .table th, .table td {
            text-align: right;
        }

        /* Navigation alignment */
        .nav-link {
            text-align: right;
        }

        /* Alert positioning */
        .alert {
            text-align: right;
        }

        /* Card content alignment */
        .card-body {
            text-align: right;
        }

        /* Modal alignment */
        .modal-header, .modal-body, .modal-footer {
            text-align: right;
        }

        /* Breadcrumb alignment */
        .breadcrumb {
            direction: rtl;
        }

        .breadcrumb-item {
            float: right;
        }

        /* Pagination alignment */
        .pagination {
            justify-content: center;
        }

        /* Status badges */
        .badge {
            font-family: 'Vazirmatn', sans-serif;
        }

        /* Toast notifications */
        .toast {
            text-align: right;
        }

        /* Loading spinner */
        .spinner-border {
            margin-left: auto;
            margin-right: auto;
        }

        /* Progress bars */
        .progress {
            direction: ltr;
        }

        /* Custom focus styles */
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Dark mode adjustments */
        @media (prefers-color-scheme: dark) {
            ::-webkit-scrollbar-track {
                background: #374151;
            }

            ::-webkit-scrollbar-thumb {
                background: #6b7280;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #9ca3af;
            }
        }
    </style>
</head>
<body class="font-sans antialiased rtl">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="fixed top-4 left-4 z-50 max-w-sm w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-green-500 ml-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">موفقیت</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="fixed top-4 left-4 z-50 max-w-sm w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-red-500 ml-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm1.41-1.41A8 8 0 1 0 15.66 4.34 8 8 0 0 0 4.34 15.66zm9.9-8.49L11.41 10l2.83 2.83-1.41 1.41L10 11.41l-2.83 2.83-1.41-1.41L8.59 10 5.76 7.17l1.41-1.41L10 8.59l2.83-2.83 1.41 1.41z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">خطا</p>
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('warning'))
        <div class="fixed top-4 left-4 z-50 max-w-sm w-full bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="py-1">
                    <svg class="fill-current h-6 w-6 text-yellow-500 ml-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold">هشدار</p>
                    <p class="text-sm">{{ session('warning') }}</p>
                </div>
            </div>
        </div>
    @endif

    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);

        // Persian number formatting
        function formatPersianNumber(num) {
            const persianDigits = '۰۱۲۳۴۵۶۷۸۹';
            const englishDigits = '0123456789';

            return num.toString().replace(/[0-9]/g, function(w) {
                return persianDigits[englishDigits.indexOf(w)];
            });
        }

        // Format all numbers on page load
        document.addEventListener('DOMContentLoaded', function() {
            const numberElements = document.querySelectorAll('.persian-numbers');
            numberElements.forEach(function(element) {
                const text = element.textContent;
                const numbers = text.match(/\d+/g);
                if (numbers) {
                    let formattedText = text;
                    numbers.forEach(function(num) {
                        formattedText = formattedText.replace(num, formatPersianNumber(num));
                    });
                    element.textContent = formattedText;
                }
            });
        });
    </script>
</body>
</html>

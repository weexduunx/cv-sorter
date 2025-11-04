<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CV Sorter - Système Universel de Tri de CV</title>
    <meta name="description" content="Système universel d'analyse et tri de CV par IA pour tous secteurs d'activité">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    <nav class="nav-glass sticky top-0 z-50 mb-4 sm:mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <div class="bg-blue-50 p-2 sm:p-3 rounded-lg sm:rounded-xl border border-blue-200">
                        <i class="fas fa-file-alt text-xl sm:text-3xl text-blue-600"></i>
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-2xl font-bold text-gray-800 tracking-tight">Tri CV</h1>
                        <p class="text-xs sm:text-sm text-gray-600 hidden sm:block">Système d'Analyse CV en fonction de la fiche de poste</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-6">
                    <div class="hidden lg:flex items-center space-x-2 bg-green-50 px-3 sm:px-4 py-1 sm:py-2 rounded-full border border-green-200">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                        <span class="text-green-700 font-medium text-xs sm:text-sm hidden sm:inline">Powered by GBG - Team IT</span>
                        <span class="text-green-700 text-xs font-medium sm:hidden">GBG</span>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="relative z-10">
        {{ $slot }}
    </main>

    <!-- Background decoration fixes -->

    @livewireScripts
</body>
</html>
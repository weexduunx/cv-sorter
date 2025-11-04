<div class="max-w-7xl mx-auto p-4 sm:p-6 space-y-4 sm:space-y-8">

    <!-- Flash Messages avec Glassmorphism -->
    @if (session()->has('message'))
        <div class="alert-glass-success rounded-xl sm:rounded-2xl px-4 sm:px-6 py-3 sm:py-4 animate-slide-in-up" role="alert">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <i class="fas fa-check-circle text-lg sm:text-2xl"></i>
                <span class="font-medium text-sm sm:text-base">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert-glass-error rounded-xl sm:rounded-2xl px-4 sm:px-6 py-3 sm:py-4 animate-slide-in-up" role="alert">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <i class="fas fa-exclamation-circle text-lg sm:text-2xl"></i>
                <span class="font-medium text-sm sm:text-base">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($showJobUpload || !$jobPosting)
        <!-- Section Upload Fiche de Poste -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 animate-fade-in-scale">
            <div class="text-center mb-6 sm:mb-8">
                <div class="glass-card w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-file-alt text-2xl sm:text-4xl text-gray-700"></i>
                </div>
                <h2 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-3">
                    <i class="fas fa-upload mr-1 sm:mr-2 text-blue-600 text-lg sm:text-xl"></i>
                    Étape 1 : Charger la fiche de poste
                </h2>
                <p class="text-gray-600 text-sm sm:text-lg px-4 sm:px-0">Commencez par uploader votre fiche de poste pour analyser automatiquement le secteur d'activité</p>
            </div>

            <div class="drop-zone rounded-2xl sm:rounded-3xl p-6 sm:p-12 text-center transition-all duration-300"
                 x-data="{ dragging: false }"
                 :class="dragging ? 'active' : ''"
                 @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="dragging = false">

                <input
                    type="file"
                    wire:model="jobPostingFile"
                    accept=".pdf"
                    class="hidden"
                    id="job-posting-upload"
                >

                <label for="job-posting-upload" class="cursor-pointer">
                    <div class="glass-card w-16 h-16 sm:w-24 sm:h-24 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
                        <i class="fas fa-cloud-upload-alt text-3xl sm:text-5xl text-blue-600"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">
                        Glissez votre fiche de poste ici
                    </h3>
                    <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">ou cliquez pour parcourir vos fichiers</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-2 text-xs sm:text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-file-pdf text-red-500"></i>
                            <span>Fichiers PDF uniquement</span>
                        </div>
                        <span class="hidden sm:inline">•</span>
                        <span>Maximum 5 Mo</span>
                    </div>
                </label>
            </div>

            <div wire:loading wire:target="jobPostingFile" class="mt-4 sm:mt-6 text-center">
                <div class="loading-glass rounded-xl sm:rounded-2xl p-4 sm:p-6 animate-pulse">
                    <i class="fas fa-spinner fa-spin text-2xl sm:text-3xl text-blue-600 mx-auto mb-2"></i>
                    <p class="text-gray-700 font-medium text-sm sm:text-base">Analyse du fichier en cours...</p>
                </div>
            </div>

            @if($jobPostingFile)
                <div class="mt-4 sm:mt-6 glass-card rounded-xl sm:rounded-2xl p-4 sm:p-6 animate-slide-in-up">
                    <div class="flex items-center space-x-3 sm:space-x-4 mb-3 sm:mb-4">
                        <div class="glass-card p-2 sm:p-3 rounded-lg sm:rounded-xl">
                            <i class="fas fa-file-alt text-lg sm:text-2xl text-blue-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-sm sm:text-base">Fichier sélectionné</p>
                            <p class="text-gray-600 text-xs sm:text-sm truncate">{{ $jobPostingFile->getClientOriginalName() }}</p>
                        </div>
                    </div>

                    <button
                        wire:click="uploadJobPosting"
                        wire:loading.attr="disabled"
                        wire:target="uploadJobPosting"
                        class="w-full btn-glass-primary px-4 sm:px-8 py-3 sm:py-4 rounded-xl sm:rounded-2xl font-bold text-sm sm:text-lg transition-all duration-300 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="uploadJobPosting" class="flex items-center justify-center space-x-2 sm:space-x-3">
                            <i class="fas fa-check-circle text-lg sm:text-xl"></i>
                            <span>Analyser la fiche de poste</span>
                        </span>
                        <span wire:loading wire:target="uploadJobPosting" class="flex items-center justify-center space-x-2 sm:space-x-3">
                            <i class="fas fa-spinner fa-spin text-lg sm:text-xl"></i>
                            <span class="hidden sm:inline">Extraction et analyse en cours...</span>
                            <span class="sm:hidden">Analyse en cours...</span>
                        </span>
                    </button>
                </div>
            @endif

            @error('jobPostingFile')
                <div class="mt-3 sm:mt-4 alert-glass-error rounded-xl sm:rounded-2xl p-3 sm:p-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-sm sm:text-lg"></i>
                        <span class="text-xs sm:text-sm">{{ $message }}</span>
                    </div>
                </div>
            @enderror
        </div>
    @else
        <!-- Fiche de poste chargée avec analyse sectorielle -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 animate-fade-in-scale">
            <div class="flex flex-col lg:flex-row justify-between items-start space-y-4 lg:space-y-0">
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4 mb-4">
                        <div class="glass-card p-3 sm:p-4 rounded-xl sm:rounded-2xl w-fit">
                            <i class="fas fa-file-alt text-2xl sm:text-3xl text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg sm:text-2xl font-bold text-gray-800 mb-1">{{ $jobPosting->title }}</h2>
                            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                                <div class="tag-glass px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium text-green-700">
                                    <i class="fas fa-star mr-1 text-green-600"></i>
                                    <span class="hidden sm:inline">Secteur détecté automatiquement</span>
                                    <span class="sm:hidden">Auto-détecté</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-gray-600 mb-3 sm:mb-4 leading-relaxed text-sm sm:text-base">{{ Str::limit($jobPosting->description, 200) }}</p>

                    <div class="flex flex-wrap gap-2 sm:gap-3">
                        <a
                            href="{{ Storage::url($jobPosting->file_path) }}"
                            target="_blank"
                            class="btn-glass-secondary px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold flex items-center space-x-1 sm:space-x-2 hover:scale-105 transition-all duration-300 text-sm sm:text-base"
                        >
                            <i class="fas fa-external-link-alt text-xs sm:text-sm"></i>
                            <span class="hidden sm:inline">Voir la fiche complète</span>
                            <span class="sm:hidden">Voir fiche</span>
                        </a>
                    </div>
                </div>

                <button
                    wire:click="changeJobPosting"
                    class="btn-glass-secondary px-4 sm:px-6 py-2 sm:py-3 rounded-lg sm:rounded-xl font-semibold flex items-center space-x-1 sm:space-x-2 lg:ml-6 hover:scale-105 transition-all duration-300 text-sm sm:text-base w-full lg:w-auto"
                >
                    <i class="fas fa-sync-alt text-xs sm:text-sm"></i>
                    <span>Changer</span>
                </button>
            </div>
        </div>

        <!-- Section Upload CV -->
        <div class="glass-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 animate-slide-in-up">
            <div class="text-center mb-6 sm:mb-8">
                <div class="glass-card w-16 h-16 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-users text-2xl sm:text-4xl text-green-600"></i>
                </div>
                <h3 class="text-xl sm:text-3xl font-bold text-gray-800 mb-2 sm:mb-3">
                    <i class="fas fa-upload mr-1 sm:mr-2 text-green-600 text-lg sm:text-xl"></i>
                    Étape 2 : Uploader les CV candidats
                </h3>
                <p class="text-gray-600 text-sm sm:text-lg px-4 sm:px-0">Le système analysera automatiquement chaque CV selon le secteur détecté</p>
            </div>

            <div class="drop-zone rounded-2xl sm:rounded-3xl p-6 sm:p-12 text-center transition-all duration-300"
                 x-data="{ dragging: false }"
                 :class="dragging ? 'active' : ''"
                 @dragover.prevent="dragging = true"
                 @dragleave.prevent="dragging = false"
                 @drop.prevent="
                     dragging = false;
                     let files = Array.from($event.dataTransfer.files);
                     $wire.uploadMultiple('uploadedFiles', files)
                 ">

                <input
                    type="file"
                    wire:model="uploadedFiles"
                    multiple
                    accept=".pdf,.doc,.docx"
                    class="hidden"
                    id="file-upload"
                >

                <label for="file-upload" class="cursor-pointer">
                    <div class="glass-card w-16 h-16 sm:w-24 sm:h-24 rounded-xl sm:rounded-2xl flex items-center justify-center mx-auto mb-4 sm:mb-6">
                        <i class="fas fa-cloud-upload-alt text-3xl sm:text-5xl text-green-600"></i>
                    </div>
                    <h3 class="text-lg sm:text-2xl font-bold text-gray-800 mb-2 sm:mb-3">
                        Glissez vos CV ici
                    </h3>
                    <p class="text-gray-600 mb-3 sm:mb-4 text-sm sm:text-base">ou cliquez pour sélectionner plusieurs fichiers</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-1 sm:space-y-0 sm:space-x-2 text-xs sm:text-sm text-gray-500">
                        <div class="flex items-center space-x-1">
                            <i class="fas fa-file text-blue-500"></i>
                            <span>PDF, DOC, DOCX</span>
                        </div>
                        <span class="hidden sm:inline">•</span>
                        <span>Maximum 5 Mo par fichier</span>
                    </div>
                </label>
            </div>

            <div wire:loading wire:target="uploadedFiles" class="mt-6 text-center">
                <div class="loading-glass rounded-2xl p-6 animate-pulse">
                    <i class="fas fa-spinner fa-spin text-3xl text-green-600 mx-auto mb-2"></i>
                    <p class="text-gray-700 font-medium">Chargement des fichiers...</p>
                </div>
            </div>

            @if($uploadedFiles)
                <div class="mt-6 glass-card rounded-2xl p-6 animate-slide-in-up">
                    <div class="flex items-center space-x-3 mb-4">
                        <i class="fas fa-list text-2xl text-gray-700"></i>
                        <h4 class="font-bold text-gray-800 text-lg">{{ count($uploadedFiles) }} fichier(s) sélectionné(s)</h4>
                    </div>

                    <div class="space-y-2 max-h-40 overflow-y-auto mb-6">
                        @foreach($uploadedFiles as $file)
                            <div class="flex items-center space-x-3 text-gray-600">
                                <i class="fas fa-file text-lg text-blue-500"></i>
                                <span class="text-sm">{{ $file->getClientOriginalName() }}</span>
                            </div>
                        @endforeach
                    </div>

                    <button
                        wire:click="uploadResumes"
                        wire:loading.attr="disabled"
                        wire:target="uploadResumes"
                        class="w-full btn-glass-primary px-8 py-4 rounded-2xl font-bold text-lg transition-all duration-300 disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="uploadResumes" class="flex items-center justify-center space-x-3">
                            <i class="fas fa-cog fa-spin text-xl"></i>
                            <span>Analyser {{ count($uploadedFiles) }} CV avec l'IA</span>
                        </span>
                        <span wire:loading wire:target="uploadResumes" class="flex items-center justify-center space-x-3">
                            <i class="fas fa-spinner fa-spin text-xl"></i>
                            <span>Analyse intelligente en cours...</span>
                        </span>
                    </button>
                </div>
            @endif

            @error('uploadedFiles.*')
                <div class="mt-4 alert-glass-error rounded-2xl p-4">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exclamation-triangle text-lg"></i>
                        <span>{{ $message }}</span>
                    </div>
                </div>
            @enderror
        </div>

        <!-- Processing Indicator -->
        <div wire:loading wire:target="scoreResumes,uploadResumes" class="loading-glass rounded-3xl p-12 text-center animate-fade-in-scale">
            <div class="glass-card w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-cog fa-spin text-4xl text-blue-600"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Analyse intelligente en cours</h3>
            <p class="text-gray-600">Le système analyse chaque CV selon les critères détectés automatiquement</p>
            <div class="mt-6 flex justify-center space-x-2">
                <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
            </div>
        </div>

        <!-- Results Section -->
        @if(count($sortedResumes) > 0)
            <div class="space-y-6">
                <!-- Header des résultats -->
                <div class="glass-card rounded-3xl p-8 animate-slide-in-up">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                        <div class="flex items-center space-x-4">
                            <div class="glass-card p-4 rounded-2xl">
                                <i class="fas fa-star text-3xl text-yellow-500"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-gray-800">Résultats du tri intelligent</h3>
                                <p class="text-gray-600 text-lg">{{ count($sortedResumes) }} CV analysé(s) et classé(s) par pertinence</p>
                            </div>
                        </div>

                        <button
                            wire:click="scoreResumes"
                            class="btn-glass-secondary px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 hover:scale-105 transition-all duration-300"
                        >
                            <i class="fas fa-sync-alt"></i>
                            <span>Réanalyser</span>
                        </button>
                    </div>
                </div>

                <!-- CV Cards -->
                <div class="space-y-4" x-data="{ expanded: null }">
                    @foreach($sortedResumes as $index => $resume)
                        <div
                            class="glass-card rounded-3xl overflow-hidden transition-all duration-300 animate-slide-in-up hover:scale-[1.02]"
                            style="animation-delay: {{ $index * 0.1 }}s"
                            :class="expanded === {{ $index }} ? 'ring-2 ring-blue-300' : ''"
                        >
                            <!-- Card Header -->
                            <div
                                class="p-6 cursor-pointer transition-all duration-300"
                                @click="expanded = expanded === {{ $index }} ? null : {{ $index }}"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-6 flex-1">
                                        <!-- Score Badge avec effet glow -->
                                        <div class="relative">
                                            <div
                                                class="w-20 h-20 rounded-2xl flex flex-col items-center justify-center text-white font-bold score-badge
                                                @if($resume['score'] >= 70) score-excellent
                                                @elseif($resume['score'] >= 40) score-good
                                                @else score-poor
                                                @endif"
                                            >
                                                <span class="text-2xl">{{ number_format($resume['score'], 0) }}</span>
                                                <span class="text-xs opacity-80">/ 100</span>
                                            </div>
                                            <!-- Glow effect -->
                                            <div class="absolute inset-0 rounded-2xl opacity-50
                                                @if($resume['score'] >= 70) bg-green-400
                                                @elseif($resume['score'] >= 40) bg-yellow-400
                                                @else bg-red-400
                                                @endif blur-xl -z-10"></div>
                                        </div>

                                        <!-- Resume Info -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h4 class="font-bold text-2xl text-gray-800 truncate">
                                                    {{ $resume['candidate_name'] }}
                                                </h4>
                                                @if($resume['score'] >= 70)
                                                    <div class="tag-glass px-3 py-1 rounded-full text-sm font-medium text-green-700">
                                                        <i class="fas fa-star mr-1 text-yellow-500"></i>
                                                        Excellent match
                                                    </div>
                                                @elseif($resume['score'] >= 40)
                                                    <div class="tag-glass px-3 py-1 rounded-full text-sm font-medium text-orange-700">
                                                        <i class="fas fa-check mr-1 text-orange-500"></i>
                                                        Bon potentiel
                                                    </div>
                                                @else
                                                    <div class="tag-glass px-3 py-1 rounded-full text-sm font-medium text-red-700">
                                                        <i class="fas fa-question mr-1 text-red-500"></i>
                                                        À examiner
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="flex items-center space-x-4 text-sm text-gray-500 mb-3">
                                                <div class="flex items-center space-x-1">
                                                    <i class="fas fa-calendar"></i>
                                                    <span>{{ $resume['created_at'] }}</span>
                                                </div>
                                                @if(isset($resume['candidate_experience']) && $resume['candidate_experience'] > 0)
                                                    <div class="flex items-center space-x-1">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $resume['candidate_experience'] }} ans d'expérience</span>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Keywords -->
                                            @if($resume['matched_keywords'] && count($resume['matched_keywords']) > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach(array_slice($resume['matched_keywords'], 0, 6) as $keyword)
                                                        <span class="tag-glass px-3 py-1 rounded-full text-xs font-medium text-green-700">
                                                            <i class="fas fa-check mr-1 text-green-500"></i>
                                                            {{ $keyword }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($resume['matched_keywords']) > 6)
                                                        <span class="text-xs text-gray-500 py-1 px-2">
                                                            +{{ count($resume['matched_keywords']) - 6 }} autres
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500">Aucune correspondance de mots-clés détectée</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Expand Icon -->
                                    <i
                                        class="fas fa-chevron-down text-2xl text-gray-400 transition-transform duration-300 flex-shrink-0 ml-4"
                                        :class="expanded === {{ $index }} ? 'rotate-180' : ''"
                                    ></i>
                                </div>
                            </div>

                            <!-- Expanded Content -->
                            <div
                                x-show="expanded === {{ $index }}"
                                x-collapse
                                class="border-t border-gray-200"
                            >
                                <div class="p-6 space-y-6">
                                    <!-- Score Breakdown -->
                                    @if(isset($resume['score_breakdown']) && !empty($resume['score_breakdown']))
                                        <div class="glass-card rounded-2xl p-6">
                                            <h5 class="font-bold text-gray-800 mb-4 flex items-center space-x-2">
                                                <i class="fas fa-chart-bar text-blue-600"></i>
                                                <span>Détail du score</span>
                                            </h5>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-800 mb-1">
                                                        {{ number_format($resume['score_breakdown']['lexical'] ?? 0, 1) }}/50
                                                    </div>
                                                    <div class="text-sm text-gray-600">Mots-clés</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-800 mb-1">
                                                        {{ number_format($resume['score_breakdown']['frequency'] ?? 0, 1) }}/30
                                                    </div>
                                                    <div class="text-sm text-gray-600">Fréquence</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-gray-800 mb-1">
                                                        {{ number_format($resume['score_breakdown']['experience'] ?? 0, 1) }}/20
                                                    </div>
                                                    <div class="text-sm text-gray-600">Expérience</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="flex flex-wrap gap-3 justify-between items-center">
                                        <div class="flex gap-3">
                                            <a
                                                href="{{ Storage::url($resume['file_path']) }}"
                                                target="_blank"
                                                class="btn-glass-primary px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 hover:scale-105 transition-all duration-300"
                                            >
                                                <i class="fas fa-external-link-alt"></i>
                                                <span>Voir le CV</span>
                                            </a>

                                            <button
                                                class="btn-glass-secondary px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 hover:scale-105 transition-all duration-300"
                                            >
                                                <i class="fas fa-star"></i>
                                                <span>Sélectionner</span>
                                            </button>
                                        </div>

                                        <button
                                            wire:click="deleteResume({{ $resume['id'] }})"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce CV ?')"
                                            class="text-red-500 hover:text-red-600 p-3 rounded-xl hover:bg-red-50 transition-all duration-300 flex items-center space-x-2"
                                        >
                                            <i class="fas fa-trash text-lg"></i>
                                            <span class="text-sm font-medium">Supprimer</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="glass-card rounded-3xl p-12 text-center animate-fade-in-scale">
                <div class="glass-card w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-folder-open text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-3">Aucun CV analysé</h3>
                <p class="text-gray-600 text-lg mb-6">Commencez par télécharger des CV pour voir la magie opérer</p>
                <div class="flex items-center justify-center space-x-2 text-sm text-gray-500">
                    <i class="fas fa-magic text-purple-500"></i>
                    <span>Le système s'adapte automatiquement à tous les secteurs d'activité</span>
                </div>
            </div>
        @endif
    @endif
</div>
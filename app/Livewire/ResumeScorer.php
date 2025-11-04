<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\JobPosting;
use App\Models\Resume;
use App\Models\ResumeScore;
use App\Services\ResumeMatchingService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ResumeScorer extends Component
{
    use WithFileUploads;
    
    public $jobPosting;
    public $jobPostingFile;
    public $uploadedFiles = [];
    public $sortedResumes = [];
    public $isProcessing = false;
    public $showJobUpload = true;
    
    protected $listeners = ['refreshResumes' => 'loadResumes'];
    
    public function mount()
    {
        // Charger la dernière fiche de poste ou créer une vide
        $this->jobPosting = JobPosting::latest()->first();
        
        if ($this->jobPosting && $this->jobPosting->file_path) {
            $this->showJobUpload = false;
            $this->loadResumes();
        }
    }
    
    public function uploadJobPosting()
    {
        $this->validate([
            'jobPostingFile' => 'required|mimes:pdf|max:5120'
        ], [
            'jobPostingFile.required' => 'Veuillez sélectionner une fiche de poste',
            'jobPostingFile.mimes' => 'Seuls les fichiers PDF sont acceptés',
            'jobPostingFile.max' => 'La taille maximale est de 5 Mo'
        ]);
        
        $this->isProcessing = true;
        
        $matcher = new ResumeMatchingService();
        
        // Stocker le fichier
        $filePath = $this->jobPostingFile->store('job-postings', 'public');
        
        // Extraire le texte
        $extractedText = $matcher->extractTextFromFile('public/' . $filePath);
        
        // Créer ou mettre à jour la fiche de poste
        $this->jobPosting = JobPosting::create([
            'title' => pathinfo($this->jobPostingFile->getClientOriginalName(), PATHINFO_FILENAME),
            'file_path' => $filePath,
            'extracted_text' => $extractedText,
            'description' => substr($extractedText, 0, 500) // Aperçu
        ]);
        
        $this->jobPostingFile = null;
        $this->showJobUpload = false;
        $this->isProcessing = false;
        
        session()->flash('message', 'Fiche de poste chargée avec succès ! Vous pouvez maintenant uploader des CV.');
    }
    
    public function changeJobPosting()
    {
        $this->showJobUpload = true;
        $this->sortedResumes = [];
    }
    
    public function uploadResumes()
    {
        if (!$this->jobPosting) {
            session()->flash('error', 'Veuillez d\'abord uploader une fiche de poste.');
            return;
        }
        
        $this->validate([
            'uploadedFiles.*' => 'required|mimes:pdf,doc,docx|max:5120'
        ], [
            'uploadedFiles.*.required' => 'Veuillez sélectionner au moins un fichier',
            'uploadedFiles.*.mimes' => 'Seuls les fichiers PDF, DOC et DOCX sont acceptés',
            'uploadedFiles.*.max' => 'La taille maximale est de 5 Mo par fichier'
        ]);
        
        $matcher = new ResumeMatchingService();
        
        foreach ($this->uploadedFiles as $file) {
            try {
                // Stocker le fichier
                $filePath = $file->store('resumes', 'public');

                // Extraire le texte avec gestion d'erreur
                $contentText = $matcher->extractTextFromFile('public/' . $filePath);

                // Vérifier que le texte est valide
                if (empty($contentText)) {
                    Log::warning('Impossible d\'extraire le texte du fichier: ' . $file->getClientOriginalName());
                    continue;
                }

                // Créer le CV
                $resume = Resume::create([
                    'candidate_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                    'file_path' => $filePath,
                    'content_text' => $contentText
                ]);

                // Calculer le score immédiatement
                $matchResult = $matcher->calculateMatch($contentText, $this->jobPosting);

                ResumeScore::create([
                    'resume_id' => $resume->id,
                    'job_posting_id' => $this->jobPosting->id,
                    'score' => $matchResult['score'],
                    'matched_keywords' => $matchResult['keywords'],
                    'detected_sector' => $matchResult['detected_sector'],
                    'score_breakdown' => $matchResult['score_breakdown'],
                    'candidate_experience' => $matchResult['candidate_experience'],
                    'required_experience' => $matchResult['required_experience'],
                    'analysis_data' => $matchResult['analysis_data']
                ]);

            } catch (\Exception $e) {
                Log::error('Erreur lors du traitement du fichier: ' . $file->getClientOriginalName() . ' - ' . $e->getMessage());
                // Continuer avec le fichier suivant au lieu de planter
                continue;
            }
        }
        
        $this->uploadedFiles = [];
        $this->loadResumes();
        
        session()->flash('message', 'CV téléchargés et analysés avec succès !');
    }
    
    public function scoreResumes()
    {
        if (!$this->jobPosting) {
            session()->flash('error', 'Veuillez d\'abord uploader une fiche de poste.');
            return;
        }
        
        $this->isProcessing = true;
        
        $matcher = new ResumeMatchingService();
        
        // Récupérer tous les CV
        $resumes = Resume::all();
        
        foreach ($resumes as $resume) {
            // Supprimer l'ancien score pour ce poste
            ResumeScore::where('resume_id', $resume->id)
                      ->where('job_posting_id', $this->jobPosting->id)
                      ->delete();
            
            // Calculer le nouveau score
            $matchResult = $matcher->calculateMatch(
                $resume->content_text,
                $this->jobPosting
            );
            
            ResumeScore::create([
                'resume_id' => $resume->id,
                'job_posting_id' => $this->jobPosting->id,
                'score' => $matchResult['score'],
                'matched_keywords' => $matchResult['keywords'],
                'detected_sector' => $matchResult['detected_sector'],
                'score_breakdown' => $matchResult['score_breakdown'],
                'candidate_experience' => $matchResult['candidate_experience'],
                'required_experience' => $matchResult['required_experience'],
                'analysis_data' => $matchResult['analysis_data']
            ]);
        }
        
        $this->loadResumes();
        $this->isProcessing = false;
        
        session()->flash('message', 'Analyse terminée !');
    }
    
    public function deleteResume($resumeId)
    {
        $resume = Resume::find($resumeId);
        
        if ($resume) {
            // Supprimer le fichier
            Storage::disk('public')->delete($resume->file_path);
            
            // Supprimer le CV
            $resume->delete();
            
            $this->loadResumes();
            session()->flash('message', 'CV supprimé avec succès');
        }
    }
    
    private function loadResumes()
    {
        if (!$this->jobPosting) {
            $this->sortedResumes = [];
            return;
        }
        
        $this->sortedResumes = Resume::with(['scores' => function($query) {
            $query->where('job_posting_id', $this->jobPosting->id);
        }])
        ->get()
        ->map(function($resume) {
            $score = $resume->scores->first();
            return [
                'id' => $resume->id,
                'candidate_name' => $resume->candidate_name,
                'email' => $resume->email,
                'file_path' => $resume->file_path,
                'score' => $score ? $score->score : 0,
                'matched_keywords' => $score ? $score->matched_keywords : [],
                'detected_sector' => $score ? $score->detected_sector : 'unknown',
                'score_breakdown' => $score ? $score->score_breakdown : [],
                'candidate_experience' => $score ? $score->candidate_experience : 0,
                'required_experience' => $score ? $score->required_experience : 0,
                'analysis_data' => $score ? $score->analysis_data : [],
                'created_at' => $resume->created_at->format('d/m/Y H:i')
            ];
        })
        ->sortByDesc('score')
        ->values()
        ->all();
    }
    
    public function render()
    {
        return view('livewire.resume-scorer');
    }
}
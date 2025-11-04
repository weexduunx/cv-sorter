<?php

use Illuminate\Support\Facades\Route;
use App\Models\JobPosting;

Route::get('/', function () {
    // Créer un poste d'exemple si aucun n'existe
    $jobPosting = JobPosting::firstOrCreate(
        ['title' => 'Développeur Full Stack Laravel'],
        [
            'description' => 'Nous recherchons un développeur Full Stack expérimenté avec Laravel, Vue.js et Tailwind CSS pour rejoindre notre équipe dynamique.',
            'required_skills' => ['Laravel', 'PHP', 'Vue.js', 'MySQL', 'Tailwind CSS', 'Git', 'API REST'],
            'experience_required' => 3
        ]
    );
    
    return view('home', ['jobPosting' => $jobPosting]);
});

Route::get('/job/{jobPosting}', function (JobPosting $jobPosting) {
    return view('home', ['jobPosting' => $jobPosting]);
})->name('job.show');
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_path',
        'extracted_text',
        'description',
        'required_skills',
        'experience_required'
    ];

    protected $casts = [
        'required_skills' => 'array'
    ];

    public function resumes()
    {
        return $this->belongsToMany(Resume::class, 'resume_scores')
                    ->withPivot('score', 'matched_keywords')
                    ->withTimestamps();
    }

    public function scores()
    {
        return $this->hasMany(ResumeScore::class);
    }
}
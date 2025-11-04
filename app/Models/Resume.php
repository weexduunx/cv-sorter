<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resume extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_name',
        'email',
        'file_path',
        'content_text'
    ];

    public function jobPostings()
    {
        return $this->belongsToMany(JobPosting::class, 'resume_scores')
                    ->withPivot('score', 'matched_keywords')
                    ->withTimestamps();
    }

    public function scores()
    {
        return $this->hasMany(ResumeScore::class);
    }
}
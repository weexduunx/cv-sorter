<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumeScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'resume_id',
        'job_posting_id',
        'score',
        'matched_keywords',
        'detected_sector',
        'score_breakdown',
        'candidate_experience',
        'required_experience',
        'analysis_data'
    ];

    protected $casts = [
        'matched_keywords' => 'array',
        'score_breakdown' => 'array',
        'analysis_data' => 'array',
        'score' => 'decimal:2',
        'candidate_experience' => 'integer',
        'required_experience' => 'integer'
    ];

    public function resume()
    {
        return $this->belongsTo(Resume::class);
    }

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }
}
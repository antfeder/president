<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'surveys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'identifier',
        'sponsor',
        'sample',
        'start_date',
        'end_date',
    ];

    /**
     * Get all candidates for the survey.
     */
    public function candidates()
    {
        return $this->belongsToMany(Candidate::class)
            ->withPivot('stat')
            ->withTimestamps();
    }

    /**
     * Check candidate existance
     *
     * @param integer $candidateId
     * @return boolean
     */
    public function hasCandidate(int $candidateId)
    {
        return $this->candidates->where('id', $candidateId)->count() > 0;
    }

    /**
     * Get specific candidate relation
     *
     * @param integer $candidateId
     * @return void
     */
    public function getCandidate(int $candidateId)
    {
        return $this->candidates->where('id', $candidateId)->first();
    }

    /**
     * Get formatted survey rolling period
     *
     * @return void
     */
    public function getRollingPeriod()
    {
        return implode(' ', [$this->start_date, $this->end_date]) ;
    }
}

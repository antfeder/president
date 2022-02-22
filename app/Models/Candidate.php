<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Survey;

class Candidate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'candidates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'politic',
    ];

    /**
     * Get all surveys for the candidate.
     */
    public function surveys()
    {
        return $this->belongsToMany(Survey::class)
            ->withPivot('stat')
            ->withTimestamps();
    }
}

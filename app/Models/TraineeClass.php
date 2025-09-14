<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class TraineeClass extends Pivot
{
    protected $table = 'trainee_classes';

    protected $fillable = [
        'trainee_id',
        'class_id',
    ];

    public function trainee()
    {
        return $this->belongsTo(Trainee::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }
}

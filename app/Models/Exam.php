<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'exam_name',
        'exam_date',
    ];

    protected $casts = [
        'exam_date' => 'date',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function trainees()
    {
        return $this->belongsToMany(Trainee::class, 'trainee_classes', 'class_id', 'trainee_id')
            ->where('trainee_classes.class_id', $this->class_id);
    }
}

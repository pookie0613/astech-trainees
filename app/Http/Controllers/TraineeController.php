<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\ClassModel;
use App\Models\TraineeClass;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TraineeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $trainees = Trainee::with(['classes', 'results'])->get();
        if ($request->has('class_id')) {
            $trainees = $trainees->whereHas('classes', function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            });
        }
        return response()->json($trainees);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainees',
            'dob' => 'required|date',
            'class_ids' => 'array|exists:classes,id'
        ]);

        $trainee = Trainee::create($request->except('class_ids'));

        // Enroll in classes if provided
        if ($request->has('class_ids')) {
            foreach ($request->class_ids as $classId) {
                TraineeClass::create([
                    'trainee_id' => $trainee->id,
                    'class_id' => $classId
                ]);
            }
        }

        $trainee->load(['classes', 'results']);
        return response()->json($trainee, 201);
    }

    public function show(Trainee $trainee): JsonResponse
    {
        $trainee->load(['classes', 'results', 'exams']);
        return response()->json($trainee);
    }

    public function update(Request $request, Trainee $trainee): JsonResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:trainees,email,' . $trainee->id,
            'dob' => 'required|date',
            'class_ids' => 'array|exists:classes,id'
        ]);

        $trainee->update($request->except('class_ids'));

        // Update class enrollments if provided
        if ($request->has('class_ids')) {
            // Remove existing enrollments
            TraineeClass::where('trainee_id', $trainee->id)->delete();

            // Add new enrollments
            foreach ($request->class_ids as $classId) {
                TraineeClass::create([
                    'trainee_id' => $trainee->id,
                    'class_id' => $classId
                ]);
            }
        }

        $trainee->load(['classes', 'results']);
        return response()->json($trainee);
    }

    public function destroy(Trainee $trainee): JsonResponse
    {
        $trainee->delete();
        return response()->json(null, 204);
    }

    public function enrollInClass(Request $request, Trainee $trainee): JsonResponse
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id'
        ]);

        $existing = TraineeClass::where('trainee_id', $trainee->id)
            ->where('class_id', $request->class_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Trainee already enrolled in this class'], 400);
        }

        TraineeClass::create([
            'trainee_id' => $trainee->id,
            'class_id' => $request->class_id
        ]);

        $trainee->load(['classes', 'results']);
        return response()->json($trainee);
    }

    public function removeFromClass(Trainee $trainee, ClassModel $class): JsonResponse
    {
        TraineeClass::where('trainee_id', $trainee->id)
            ->where('class_id', $class->id)
            ->delete();

        $trainee->load(['classes', 'results']);
        return response()->json($trainee);
    }

    public function getClasses(Trainee $trainee): JsonResponse
    {
        $classes = $trainee->classes()->with('course')->get();
        return response()->json($classes);
    }

    public function getResults(Trainee $trainee): JsonResponse
    {
        $results = $trainee->results()->with('exam')->get();
        return response()->json($results);
    }
}

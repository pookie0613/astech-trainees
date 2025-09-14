<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Trainee;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ResultController extends Controller
{
    public function index(): JsonResponse
    {
        $results = Result::with(['trainee', 'exam'])->get();
        return response()->json($results);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'trainee_id' => 'required|exists:trainees,id',
            'result' => 'nullable|string|max:255'
        ]);

        // Check if result already exists for this trainee and exam
        $existing = Result::where('exam_id', $request->exam_id)
            ->where('trainee_id', $request->trainee_id)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Result already exists for this trainee and exam'], 400);
        }

        $result = Result::create($request->all());
        $result->load(['trainee', 'exam']);
        
        return response()->json($result, 201);
    }

    public function show(Result $result): JsonResponse
    {
        $result->load(['trainee', 'exam']);
        return response()->json($result);
    }

    public function update(Request $request, Result $result): JsonResponse
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'trainee_id' => 'required|exists:trainees,id',
            'result' => 'nullable|string|max:255'
        ]);

        $result->update($request->all());
        $result->load(['trainee', 'exam']);
        
        return response()->json($result);
    }

    public function destroy(Result $result): JsonResponse
    {
        $result->delete();
        return response()->json(null, 204);
    }

    public function getByTrainee(Trainee $trainee): JsonResponse
    {
        $results = $trainee->results()->with('exam')->get();
        return response()->json($results);
    }

    public function getByExam(Exam $exam): JsonResponse
    {
        $results = $exam->results()->with('trainee')->get();
        return response()->json($results);
    }

    public function bulkCreate(Request $request): JsonResponse
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.exam_id' => 'required|exists:exams,id',
            'results.*.trainee_id' => 'required|exists:trainees,id',
            'results.*.result' => 'nullable|string|max:255'
        ]);

        $createdResults = [];
        
        foreach ($request->results as $resultData) {
            // Check if result already exists
            $existing = Result::where('exam_id', $resultData['exam_id'])
                ->where('trainee_id', $resultData['trainee_id'])
                ->first();

            if (!$existing) {
                $result = Result::create($resultData);
                $result->load(['trainee', 'exam']);
                $createdResults[] = $result;
            }
        }

        return response()->json($createdResults, 201);
    }
}

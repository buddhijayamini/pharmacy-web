<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\MedicationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    protected $medicationRepository;

    public function __construct(MedicationRepository $medicationRepository)
    {
        $this->middleware('auth');
        $this->medicationRepository = $medicationRepository;
    }

    public function index()
    {
        try {
            return $this->medicationRepository->all();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch medications.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $medication = $this->medicationRepository->create($request->all());

            DB::commit();

            return response()->json($medication, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to store medication.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $medication = $this->medicationRepository->find($id);
            if (!$medication) {
                throw new \Exception('Medication not found.', 404);
            }

            $updatedMedication = $this->medicationRepository->update($medication, $request->all());

            DB::commit();

            return response()->json($updatedMedication, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $medication = $this->medicationRepository->find($id);
            $this->medicationRepository->delete($medication);

            DB::commit();

            return response()->json(null, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete medication.'], 500);
        }
    }
}

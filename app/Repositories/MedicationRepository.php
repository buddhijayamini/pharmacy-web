<?php

namespace App\Repositories;

use App\Models\Medication;

class MedicationRepository implements MedicationInterface
{
    public function all()
    {
        return Medication::all();
    }

    public function create(array $data)
    {
        return Medication::create($data);
    }

    public function find($id)
    {
        return Medication::find($id);
    }

    public function update(Medication $medication, array $data)
    {
        $medication->update($data);
        return $medication;
    }

    public function delete(Medication $medication)
    {
        $medication->delete();
    }
}

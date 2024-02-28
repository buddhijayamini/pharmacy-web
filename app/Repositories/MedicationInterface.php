<?php

namespace App\Repositories;

use App\Models\Medication;

interface MedicationInterface
{
    public function all();

    public function create(array $data);

    public function find($id);

    public function update(Medication $medication, array $data);

    public function delete(Medication $medication);
}

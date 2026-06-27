<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
    'make',
    'model',
    'year',
    'kilometers', // ou 'mileage' conforme mudaste na DB
    'plate_number',
    'iuc_paid',
    'next_inspection_date',
    'inspection_done',
    'user_id' // se os carros estiverem associados a um utilizador
];
}

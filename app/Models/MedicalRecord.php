<?php

// app/Models/MedicalRecord.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'blood_type', 'allergies', 'medical_history',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}

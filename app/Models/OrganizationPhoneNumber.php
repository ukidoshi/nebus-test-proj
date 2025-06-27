<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationPhoneNumber extends Model
{
    use HasFactory, SoftDeletes;

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}

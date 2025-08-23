<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    // Enable timestamps (by default, Laravel handles created_at and updated_at)
    public $timestamps = true;

    // Specify the fields that are mass assignable
    protected $fillable = ['title', 'message', 'active'];
}

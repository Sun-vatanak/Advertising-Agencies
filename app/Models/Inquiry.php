<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'message',
        'service_interested_in', 'status'
    ];

    // Default values for new inquiries
    protected $attributes = [
        'status' => 'new',
    ];
}

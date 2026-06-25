<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Quote extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'product_name', 'product_slug', 'name', 'hospital_name', 'phone',
        'email', 'message', 'qty', 'source', 'status', 'admin_notes', 'reminder_sent_at',
    ];

    protected $casts = [
        'qty' => 'integer',
        'reminder_sent_at' => 'datetime',
    ];

    protected $appends = ['_id', 'productName', 'productSlug', 'hospitalName', 'adminNotes', 'reminderSentAt', 'createdAt', 'updatedAt'];

    public function getProductNameAttribute($value) { return $this->frontendAttribute('product_name', $value); }
    public function getProductSlugAttribute($value) { return $this->frontendAttribute('product_slug', $value); }
    public function getHospitalNameAttribute($value) { return $this->frontendAttribute('hospital_name', $value); }
    public function getAdminNotesAttribute($value) { return $this->frontendAttribute('admin_notes', $value); }
    public function getReminderSentAtAttribute($value) { return $this->frontendDateAttribute('reminder_sent_at'); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

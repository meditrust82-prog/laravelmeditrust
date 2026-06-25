<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasFrontendCompatibility;

class Order extends Model
{
    use HasFactory, HasFrontendCompatibility;

    protected $fillable = [
        'user_id', 'items', 'total_price', 'status', 'payment_gateway', 'payment_id',
        'payment_amount', 'payment_paid_at', 'payment_expires_at', 'payment_webhook_event_id',
        'shipping_address', 'notes',
    ];

    protected $casts = [
        'items' => 'array',
        'shipping_address' => 'array',
        'payment_paid_at' => 'datetime',
        'payment_expires_at' => 'datetime',
        'total_price' => 'decimal:2',
        'payment_amount' => 'decimal:2',
    ];

    protected $appends = ['_id', 'totalPrice', 'paymentGateway', 'paymentId', 'paymentAmount', 'paymentPaidAt', 'paymentExpiresAt', 'paymentWebhookEventId', 'shippingAddress', 'createdAt', 'updatedAt'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalPriceAttribute($value) { return $this->frontendAttribute('total_price', $value); }
    public function getPaymentGatewayAttribute($value) { return $this->frontendAttribute('payment_gateway', $value); }
    public function getPaymentIdAttribute($value) { return $this->frontendAttribute('payment_id', $value); }
    public function getPaymentAmountAttribute($value) { return $this->frontendAttribute('payment_amount', $value); }
    public function getPaymentPaidAtAttribute($value) { return $this->frontendDateAttribute('payment_paid_at'); }
    public function getPaymentExpiresAtAttribute($value) { return $this->frontendDateAttribute('payment_expires_at'); }
    public function getPaymentWebhookEventIdAttribute($value) { return $this->frontendAttribute('payment_webhook_event_id', $value); }
    public function getShippingAddressAttribute($value) { return $this->frontendJsonAttribute('shipping_address'); }
    public function getCreatedAtAttribute($value) { return $this->frontendDateAttribute('created_at'); }
    public function getUpdatedAtAttribute($value) { return $this->frontendDateAttribute('updated_at'); }
}

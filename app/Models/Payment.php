<?php

namespace App\Models;


use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'payment_id',
        'project_id',
        'details',
        'amount',
        'currency',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function applyFilters(QueryBuilder | Builder $query): QueryBuilder | Builder
    {
        $filters = [
            'payment_id' => fn($q, $value) => $q->where('payment_id', 'like', "%{$value}%"),
            'details' => fn($q, $value) => $q->where('details', 'like', "%{$value}%"),
            'email' => fn($q, $value) => $q->whereHas('project.user', fn($q) => $q->where('email', 'like', "%{$value}%")),
            'currency' => fn($q, $value) => $q->where('currency', '=', $value),
            'project_id' => fn($q, $value) => $q->where('project_id', '=', $value),
        ];

        foreach ($filters as $field => $filter) {
            if (request()->filled($field)) {
                $filter($query, request()->input($field));
            }
        }

        return $query;
    }

    public function applyBalance(): void
    {
        if ($this->status === 'Оплачен') {
            $user = $this->project->user;

            $balance = $user->balance()->firstOrCreate([
                'user_id' => $user->id,
            ]);
            

            match (strtoupper($this->currency)) {
                'RUB' => $balance->increment('balance_rub', $this->amount),
                'USD' => $balance->increment('balance_usd', $this->amount),
                'KZT' => $balance->increment('balance_kzt', $this->amount),
            };
        }
    }
}

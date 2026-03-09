<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'user_id', 'project_id', 'borrow_transaction_id',
        'return_transaction_id', 'risk_rule_id', 'points_added', 'notes',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function borrowTransaction()
    {
        return $this->belongsTo(BorrowTransaction::class);
    }

    public function returnTransaction()
    {
        return $this->belongsTo(ReturnTransaction::class);
    }

    public function riskRule()
    {
        return $this->belongsTo(RiskRule::class);
    }
}

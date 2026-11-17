<?php

namespace Modules\Shop\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Shop\Models\Concerns\BelongsToShop;

class Expense extends Model
{
    use BelongsToShop;

    public const CATEGORIES = ['Rent', 'Salary', 'Electricity', 'Internet', 'Transport', 'Maintenance', 'Marketing', 'Other'];

    protected $connection = 'shop';

    protected $fillable = ['company_id', 'category', 'amount', 'description', 'expense_date', 'reference'];

    protected $casts = ['expense_date' => 'date', 'amount' => 'decimal:2'];
}

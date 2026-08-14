<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['year', 'last_number'])]
class FinanceSequence extends Model
{
    use HasFactory;

    protected $table = 'financial_sequences';
}

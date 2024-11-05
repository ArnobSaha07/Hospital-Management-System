<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Faqs
 *
 * @property int $id
 * @property string $question
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs query()
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Faqs whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Faqs extends Model
{
    use HasFactory;

    public $table = 'faqs';

    public $fillable = [
        'question',
        'answer',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'integer',
        'question' => 'string',
        'answer' => 'string',
    ];

    /**
     * @var string[]
     */
    public static $rules = [
        'question' => 'required|string|max:200',
        'answer' => 'required|string|max:500',
    ];
}

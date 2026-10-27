<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Блок задания в уроке: домашнее задание или задание на уроке.
 *
 * @property int $id
 * @property int $lesson_id
 * @property string $kind homework|classwork
 * @property string $type memorize|question|video|text
 * @property int $order
 * @property string $title
 * @property string|null $body
 * @property string|null $url
 */
class Task extends Model
{
    use HasFactory;

    /** Домашнее задание и задание на уроке */
    public const KINDS = ['homework', 'classwork'];

    /** Выучить наизусть, вопрос с ответом, видео, просто текст */
    public const TYPES = ['memorize', 'question', 'video', 'text'];

    protected $fillable = [
        'lesson_id',
        'kind',
        'type',
        'order',
        'title',
        'body',
        'url',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /** Отвечать ученик может только на вопрос */
    public function isAnswerable(): bool
    {
        return $this->type === 'question';
    }
}

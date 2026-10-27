<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ответ ученика на задание.
 *
 * Черновик сохраняется сам по мере набора, «Сдать» переводит в submitted,
 * учитель отмечает checked и может оставить комментарий.
 *
 * @property int $id
 * @property int $task_id
 * @property int $user_id
 * @property int|null $group_id
 * @property string|null $answer
 * @property string $status draft|submitted|checked
 * @property string|null $comment
 */
class Answer extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_CHECKED = 'checked';

    protected $fillable = [
        'task_id',
        'user_id',
        'group_id',
        'answer',
        'status',
        'submitted_at',
        'checked_at',
        'checked_by',
        'comment',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'checked_at' => 'datetime',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}

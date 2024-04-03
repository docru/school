<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $course_id Курс
 * @property string $tasks Задания
 * @method static \Illuminate\Database\Eloquent\Builder|Control newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Control newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Control query()
 * @method static \Illuminate\Database\Eloquent\Builder|Control whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Control whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Control whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Control whereTasks($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Control whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Control extends Model
{
    use HasFactory;
}

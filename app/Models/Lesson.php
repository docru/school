<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $course_id Курс
 * @property int|null $module_id Модуль
 * @property string $name Название урока
 * @property float|null $hours Количество учебных часов
 * @property int $order Порядок
 * @property string|null $methodical_description Методическое описание
 * @property string|null $abstract Конспект
 * @property int|null $school_day Учебный день
 * @property int|null $school_day_order Сортировка в учебном дне
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\Module|null $module
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereAbstract($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereMethodicalDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereModuleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereSchoolDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereSchoolDayOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lesson whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Lesson extends Model
{
    use HasFactory;

    public $fillable = [
        'module_id',
        'name',
        'hours',
        'methodical_description',
        'abstract',
        'order',
        'school_day',
        'school_day_order',
    ];

    protected function methodicalDescription(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => $value . "\n",
            set: fn(string $value) => trim($value),
        );
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }
}

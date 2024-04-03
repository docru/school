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
 * @property int $group_id Группа
 * @property string $date Дата
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay query()
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SchoolDay whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SchoolDay extends Model
{
    use HasFactory;
}

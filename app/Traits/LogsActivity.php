<?php

namespace App\Traits;

use App\Services\ActivityLogger;

trait LogsActivity
{
    protected static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            ActivityLogger::log(
                action: 'create',
                description: 'Menambahkan '.class_basename($model).' baru: '.($model->name ?? $model->id),
                model: $model,
                newValues: $model->getAttributes(),
            );
        });

        static::updated(function ($model) {
            $changed = $model->getDirty();
            if (empty($changed)) {
                return;
            }

            $old = [];
            $new = [];
            foreach ($changed as $key => $value) {
                $old[$key] = $model->getOriginal($key);
                $new[$key] = $value;
            }

            ActivityLogger::log(
                action: 'update',
                description: 'Memperbarui '.class_basename($model).': '.($model->name ?? $model->id),
                model: $model,
                oldValues: $old,
                newValues: $new,
            );
        });

        static::deleted(function ($model) {
            ActivityLogger::log(
                action: 'delete',
                description: 'Menghapus '.class_basename($model).': '.($model->name ?? $model->id),
                model: $model,
                oldValues: $model->getAttributes(),
            );
        });
    }
}

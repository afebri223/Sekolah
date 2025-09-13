<?php
// app/Traits/Searchable.php

namespace App\Traits;

trait Searchable
{
    public function scopeSearch($query, $term)
    {
        if (empty($term)) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            foreach ($this->searchable ?? [] as $field) {
                if (str_contains($field, '.')) {
                    // Relationship search
                    $parts = explode('.', $field);
                    $q->orWhereHas($parts[0], function ($subQuery) use ($parts, $term) {
                        $subQuery->where($parts[1], 'like', "%{$term}%");
                    });
                } else {
                    $q->orWhere($field, 'like', "%{$term}%");
                }
            }
        });
    }
}
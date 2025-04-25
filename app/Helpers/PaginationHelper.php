<?php

namespace App\Helpers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginationHelper
{
    /**
     * Paginate a collection or query.
     *
     * @param mixed $items
     * @param int $perPage
     * @param int|null $page
     * @param array $options
     * @return LengthAwarePaginator
     */
    public static function paginate($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: LengthAwarePaginator::resolveCurrentPage();

        if ($items instanceof \Illuminate\Database\Eloquent\Builder || $items instanceof \Illuminate\Database\Query\Builder) {
            return $items->paginate($perPage, ['*'], 'page', $page)->appends(request()->query());
        }

        if ($items instanceof Collection) {
            $items = $items->forPage($page, $perPage);
            return new LengthAwarePaginator(
                $items,
                count($items),
                $perPage,
                $page,
                array_merge(['path' => request()->url(), 'query' => request()->query()], $options)
            );
        }

        throw new \InvalidArgumentException("Items must be a Collection or Eloquent Query Builder.");
    }
}

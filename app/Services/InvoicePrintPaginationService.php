<?php

namespace App\Services;

use Illuminate\Support\Collection;

class InvoicePrintPaginationService
{
    public const FIRST_PAGE_MAX_ROWS = 8;
    public const NEXT_PAGE_MAX_ROWS = 10;

    public function paginate(Collection $items): array
    {
        $remainingItems = $items->values();
        $pages = [];
        $pageIndex = 0;

        do {
            $limit = $pageIndex === 0 ? self::FIRST_PAGE_MAX_ROWS : self::NEXT_PAGE_MAX_ROWS;
            $pageItems = $remainingItems->splice(0, $limit);

            $pages[] = [
                'items' => $pageItems,
                'is_last' => $remainingItems->isEmpty(),
            ];

            $pageIndex++;
        } while ($remainingItems->isNotEmpty());

        return $pages;
    }
}

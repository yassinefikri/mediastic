<?php

declare(strict_types=1);

namespace App\Repository;

use LogicException;

trait RepositoryTrait
{
    private function validatePageNumber(int $page): void
    {
        if ($page < 1) {
            throw new LogicException('Page number should be positive');
        }
    }
}

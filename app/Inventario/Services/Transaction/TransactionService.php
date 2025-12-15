<?php

declare(strict_types=1);

namespace App\Inventario\Services\Transaction;

use App\Inventario\Interfaces\Services\TransactionServiceInterface;
use Illuminate\Support\Facades\DB;

class TransactionService implements TransactionServiceInterface
{
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commit(): void
    {
        DB::commit();
    }

    public function rollBack(): void
    {
        DB::rollBack();
    }

    public function transaction(callable $callback)
    {
        return DB::transaction($callback);
    }
}


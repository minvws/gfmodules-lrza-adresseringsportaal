<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    public function getAll(): JsonResponse
    {
        $perPage = request()->input('per_page', 15);
        $suppliers = Supplier::paginate($perPage);

        $transformed = $suppliers->getCollection()->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'name' => '',
                'endpoint' => $supplier->endpoint,
                'created_at' => $supplier->created_at,
                'updated_at' => $supplier->updated_at,
            ];
        });

        $suppliers->setCollection($transformed);

        return response()->json($suppliers);
    }

    public function getOne(string $supplier_id): JsonResponse
    {
        $supplier = Supplier::findOrFail($supplier_id);

        return response()->json([
            'id' => $supplier->id,
            'name' => '',
            'endpoint' => $supplier->endpoint,
            'created_at' => $supplier->created_at,
            'updated_at' => $supplier->updated_at,
        ]);
    }
}

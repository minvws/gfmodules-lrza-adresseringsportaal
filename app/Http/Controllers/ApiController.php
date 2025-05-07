<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{
    /**
     * Get all endpoints with the latest version.
     */
    public function getAllEndpoints(): JsonResponse
    {
        $perPage = request()->input('per_page', 15);

        // Fetch supplier endpoints with the latest version
        $supplier_endpoints = Supplier::where('version', Supplier::max('version'))
            ->orderBy('id')
            ->paginate($perPage);

        // Transform the collection
        $transformed = $supplier_endpoints->getCollection()->map(function ($supplier_endpoint) {
            return [
                'id' => $supplier_endpoint->id,
                'version' => $supplier_endpoint->version,
                'name' => '',
                'endpoint' => $supplier_endpoint->endpoint,
                'deleted_at' => $supplier_endpoint->deleted_at,
                'created_at' => $supplier_endpoint->created_at,
                'updated_at' => $supplier_endpoint->updated_at,
            ];
        });

        $supplier_endpoints->setCollection($transformed);

        // Return JSON response
        return response()->json($supplier_endpoints);
    }

    /**
     * Get a specific endpoint by supplier ID with the latest version.
     */
    public function getEndpointById(string $supplier_id): JsonResponse
    {
        // Fetch the supplier endpoint
        $supplier_endpoint = Supplier::where('version', Supplier::max('version'))
            ->where('id', $supplier_id)
            ->firstOrFail();

        // Return JSON response
        return response()->json([
            'id' => $supplier_endpoint->id,
            'version' => $supplier_endpoint->version,
            'name' => '',
            'endpoint' => $supplier_endpoint->endpoint,
            'deleted_at' => $supplier_endpoint->deleted_at,
            'created_at' => $supplier_endpoint->created_at,
            'updated_at' => $supplier_endpoint->updated_at,
        ]);
    }

    /**
     * Get the history of all endpoints, including deleted ones.
     */
    public function getEndpointHistory(): JsonResponse
    {
        $perPage = request()->input('per_page', 15);

        // Fetch supplier endpoints with history
        $supplier_endpoints = Supplier::withTrashed()
            ->orderBy('id')
            ->orderBy('version')
            ->paginate($perPage);

        // Transform the collection
        $transformed = $supplier_endpoints->getCollection()->map(function ($supplier_endpoint) {
            return [
                'id' => $supplier_endpoint->id,
                'version' => $supplier_endpoint->version,
                'name' => '',
                'endpoint' => $supplier_endpoint->endpoint,
                'deleted_at' => $supplier_endpoint->deleted_at,
                'created_at' => $supplier_endpoint->created_at,
                'updated_at' => $supplier_endpoint->updated_at,
            ];
        });

        $supplier_endpoints->setCollection($transformed);

        // Return JSON response
        return response()->json($supplier_endpoints);
    }

    /**
     * Get the history of a specific endpoint by supplier ID, including deleted ones.
     */
    public function getEndpointHistoryById(string $supplier_id): JsonResponse
    {
        $perPage = request()->input('per_page', 15);

        // Fetch supplier endpoint history
        $supplier_endpoint = Supplier::withTrashed()
            ->where('id', $supplier_id)
            ->orderBy('version')
            ->paginate($perPage);

        // Transform the collection
        $transformed = $supplier_endpoint->getCollection()->map(function ($supplier_endpoint) {
            return [
                'id' => $supplier_endpoint->id,
                'version' => $supplier_endpoint->version,
                'name' => '',
                'endpoint' => $supplier_endpoint->endpoint,
                'deleted_at' => $supplier_endpoint->deleted_at,
                'created_at' => $supplier_endpoint->created_at,
                'updated_at' => $supplier_endpoint->updated_at,
            ];
        });

        $supplier_endpoint->setCollection($transformed);

        // Return JSON response
        return response()->json($supplier_endpoint);
    }
}

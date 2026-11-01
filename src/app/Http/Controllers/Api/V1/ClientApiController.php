<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientApiController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Client::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = '%'.$request->string('search').'%';
                $q->where('name', 'like', $search)
                    ->orWhere('document_number', 'like', $search);
            })
            ->when($request->has('active'), fn ($q) => $q->where('active', $request->boolean('active')))
            ->orderBy('name');

        if ($request->boolean('all')) {
            $clients = $query->get();

            return $this->sendResponse([
                'total' => $clients->count(),
                'items' => $clients,
            ], 'Directorio completo de clientes.');
        }

        $perPage = min(max($request->integer('per_page', 15), 1), 100);
        $paginated = $query->paginate($perPage);

        return $this->sendResponse([
            'current_page' => $paginated->currentPage(),
            'total_items' => $paginated->total(),
            'per_page' => $paginated->perPage(),
            'last_page' => $paginated->lastPage(),
            'items' => $paginated->items(),
        ], 'Listado paginado de clientes.');
    }

    public function show(int|string $id): JsonResponse
    {
        $client = Client::find($id);

        if (! $client) {
            return $this->sendError("Cliente con ID {$id} no encontrado.", [], 404);
        }

        return $this->sendResponse($client, 'Detalle del cliente.');
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'document_type' => ['nullable', 'string', 'in:CC,NIT,CE,PP'],
            'document_number' => ['required', 'string', 'max:50', 'unique:clients,document_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'active' => ['nullable', 'boolean'],
        ], [
            'document_number.required' => 'El número de documento es obligatorio.',
            'document_number.unique' => 'Ya existe un cliente con este número de documento.',
            'name.required' => 'El nombre o razón social es obligatorio.',
        ]);

        $validated['document_type'] = $validated['document_type'] ?? 'CC';
        $validated['active'] = $validated['active'] ?? true;

        $client = Client::create($validated);

        return $this->sendResponse($client, 'Cliente registrado exitosamente.', 201);
    }
}

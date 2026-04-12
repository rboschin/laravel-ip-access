<?php

namespace Rboschin\LaravelIpAccess\Controllers;

use Rboschin\LaravelIpAccess\Models\IpAccessWhite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IpAccessWhiteController
{
    /**
     * Get all active whitelisted IP addresses.
     */
    public function index(): JsonResponse
    {
        $ipAddresses = IpAccessWhite::getActiveIpAddresses();
        
        return response()->json([
            'whitelist' => $ipAddresses,
            'count' => count($ipAddresses)
        ]);
    }

    /**
     * Store a new IP address in the whitelist.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ip_address' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $ipWhite = IpAccessWhite::create([
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'is_active' => $request->get('is_active', true)
        ]);

        return response()->json($ipWhite, 201);
    }

    /**
     * Update an IP address in the whitelist.
     */
    public function update(Request $request, IpAccessWhite $ipWhite): JsonResponse
    {
        $request->validate([
            'ip_address' => 'string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $ipWhite->update($request->all());

        return response()->json($ipWhite);
    }

    /**
     * Remove an IP address from the whitelist.
     */
    public function destroy(IpAccessWhite $ipWhite): JsonResponse
    {
        $ipWhite->delete();

        return response()->json(null, 204);
    }
}

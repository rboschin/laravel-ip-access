<?php

namespace Rboschin\LaravelIpAccess\Controllers;

use Rboschin\LaravelIpAccess\Models\IpAccessBlack;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IpAccessBlackController
{
    /**
     * Get all active blacklisted IP addresses.
     */
    public function index(): JsonResponse
    {
        $ipAddresses = IpAccessBlack::getActiveIpAddresses();
        
        return response()->json([
            'blacklist' => $ipAddresses,
            'count' => count($ipAddresses)
        ]);
    }

    /**
     * Store a new IP address in the blacklist.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'ip_address' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $ipBlack = IpAccessBlack::create([
            'ip_address' => $request->ip_address,
            'description' => $request->description,
            'is_active' => $request->get('is_active', true)
        ]);

        return response()->json($ipBlack, 201);
    }

    /**
     * Update an IP address in the blacklist.
     */
    public function update(Request $request, IpAccessBlack $ipBlack): JsonResponse
    {
        $request->validate([
            'ip_address' => 'string',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $ipBlack->update($request->all());

        return response()->json($ipBlack);
    }

    /**
     * Remove an IP address from the blacklist.
     */
    public function destroy(IpAccessBlack $ipBlack): JsonResponse
    {
        $ipBlack->delete();

        return response()->json(null, 204);
    }
}

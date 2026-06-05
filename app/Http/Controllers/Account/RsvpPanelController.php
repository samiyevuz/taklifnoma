<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Services\Rsvp\RsvpDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RsvpPanelController extends Controller
{
    public function __construct(
        private readonly RsvpDashboardService $dashboardService,
    ) {}

    public function show(Request $request, Invitation $invitation): JsonResponse
    {
        $this->authorize('view', $invitation);

        $sinceId = $request->integer('since_id');

        $payload = $this->dashboardService->snapshot($invitation);

        if ($sinceId > 0) {
            $payload['has_new'] = $invitation->rsvpResponses()
                ->where('id', '>', $sinceId)
                ->exists();
        }

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AdminStatisticsService $statistics,
    ) {}

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'title' => __('admin.dashboard_title'),
            'stats' => $this->statistics->dashboard(),
        ]);
    }

    public function stats(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->statistics->dashboard(),
        ]);
    }
}

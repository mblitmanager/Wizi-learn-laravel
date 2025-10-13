<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAppUsage;
use Illuminate\Http\Request;

class UserAppUsageAdminController extends Controller
{
    public function index(Request $request)
    {
        $platform = $request->get('platform');
        $query = UserAppUsage::with('user')
            ->when($platform, fn($q) => $q->where('platform', $platform))
            ->orderByDesc('last_used_at')
            ->orderBy('platform');

        $usages = $query->paginate(20)->withQueryString();

        return view('admin.user_app_usages.index', compact('usages', 'platform'));
    }

    public function export(Request $request)
    {
        $platform = $request->get('platform');
        $query = UserAppUsage::with('user')
            ->when($platform, fn($q) => $q->where('platform', $platform))
            ->orderByDesc('last_used_at');

        $rows = $query->get()->map(function ($u) {
            return [
                'user_id' => $u->user_id,
                'user_name' => $u->user?->name,
                'platform' => $u->platform,
                'first_used_at' => optional($u->first_used_at)->format('Y-m-d H:i:s'),
                'last_used_at' => optional($u->last_used_at)->format('Y-m-d H:i:s'),
                'app_version' => $u->app_version,
                'device_model' => $u->device_model,
                'os_version' => $u->os_version,
            ];
        })->toArray();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_app_usages.csv"',
        ];

        $callback = function () use ($rows) {
            $output = fopen('php://output', 'w');
            if (!empty($rows)) {
                fputcsv($output, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($output, $row);
                }
            } else {
                fputcsv($output, ['user_id', 'user_name', 'platform', 'first_used_at', 'last_used_at', 'app_version', 'device_model', 'os_version']);
            }
            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}

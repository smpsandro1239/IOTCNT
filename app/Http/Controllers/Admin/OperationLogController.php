<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperationLog;
use Illuminate\Http\Request;

class OperationLogController extends Controller
{
  /**
   * Display a listing of operation logs.
   */
  public function index(Request $request)
  {
    $query = OperationLog::with(['valve', 'schedule', 'user', 'telegramUser'])
      ->orderBy('logged_at', 'desc');

    // Filtros
    if ($request->filled('source')) {
      $query->where('source', $request->source);
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    if ($request->filled('event_type')) {
      $query->where('event_type', 'like', '%' . $request->event_type . '%');
    }

    if ($request->filled('date_from')) {
      $query->whereDate('logged_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
      $query->whereDate('logged_at', '<=', $request->date_to);
    }

    $logs = $query->paginate(20);

    return view('admin.logs.index', compact('logs'));
  }

  /**
   * Display the specified log.
   */
  public function show(OperationLog $log)
  {
    $log->load(['valve', 'schedule', 'user', 'telegramUser']);
    return view('admin.logs.show', compact('log'));
  }

  /**
   * Remove the specified log from storage.
   */
  public function destroy(OperationLog $log)
  {
    $log->delete();
    return redirect()->route('admin.logs.index')
      ->with('success', 'Log eliminado com sucesso.');
  }

  /**
   * Bulk delete old logs.
   */
  public function bulkDelete(Request $request)
  {
    $request->validate([
      'days_old' => 'required|integer|min:1|max:365'
    ]);

    $cutoffDate = now()->subDays($request->days_old);
    $deletedCount = OperationLog::where('logged_at', '<', $cutoffDate)->delete();

    return redirect()->route('admin.logs.index')
      ->with('success', "Eliminados {$deletedCount} logs com mais de {$request->days_old} dias.");
  }
}

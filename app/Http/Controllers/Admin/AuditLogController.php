<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\AuditLogPresenter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class AuditLogController extends Controller
{
    public function __invoke(Request $request, AuditLogPresenter $presenter): Response
    {
        $filters = [
            'search' => $request->string('search')->trim()->value(),
            'event' => $request->string('event')->trim()->value(),
            'subject' => $request->string('subject')->trim()->value(),
            'actor' => $request->string('actor')->trim()->value(),
            'date_from' => $request->date('date_from')?->toDateString() ?? '',
            'date_to' => $request->date('date_to')?->toDateString() ?? '',
        ];

        $logs = $this->query($filters)
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('admin/audit/Index', [
            'logs' => $this->formatPaginator($logs, fn (Activity $activity): array => $presenter->present($activity)),
            'filters' => $filters,
            'options' => [
                'events' => Activity::query()
                    ->whereNotNull('event')
                    ->distinct()
                    ->orderBy('event')
                    ->pluck('event')
                    ->values(),
                'subjects' => Activity::query()
                    ->whereNotNull('subject_type')
                    ->distinct()
                    ->orderBy('subject_type')
                    ->pluck('subject_type')
                    ->map(fn (string $subject): string => class_basename($subject))
                    ->unique()
                    ->values(),
            ],
            'stats' => [
                'total' => Activity::query()->count(),
                'today' => Activity::query()->whereDate('created_at', now()->toDateString())->count(),
                'this_week' => Activity::query()->where('created_at', '>=', now()->startOfWeek())->count(),
                'administrative' => Activity::query()->whereLike('description', '%administrator%')->count(),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return Builder<Activity>
     */
    private function query(array $filters): Builder
    {
        return Activity::query()
            ->with(['causer', 'subject'])
            ->when($filters['search'], function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->whereLike('description', "%{$search}%")
                        ->orWhereLike('event', "%{$search}%")
                        ->orWhereLike('log_name', "%{$search}%")
                        ->orWhere('properties->route', 'like', "%{$search}%")
                        ->orWhereHasMorph('causer', [User::class], function (Builder $query) use ($search): void {
                            $query->whereLike('name', "%{$search}%")
                                ->orWhereLike('email', "%{$search}%");
                        });
                });
            })
            ->when($filters['event'], fn (Builder $query, string $event): Builder => $query->where('event', $event))
            ->when($filters['subject'], function (Builder $query, string $subject): void {
                $query->where('subject_type', 'like', "%{$subject}");
            })
            ->when($filters['actor'], function (Builder $query, string $actor): void {
                $query->whereHasMorph('causer', [User::class], function (Builder $query) use ($actor): void {
                    $query->whereLike('name', "%{$actor}%")
                        ->orWhereLike('email', "%{$actor}%");
                });
            })
            ->when($filters['date_from'], fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '>=', $date))
            ->when($filters['date_to'], fn (Builder $query, string $date): Builder => $query->whereDate('created_at', '<=', $date));
    }

    /**
     * @template TModel
     *
     * @param  LengthAwarePaginator<TModel>  $paginator
     * @param  callable(TModel): array<string, mixed>  $map
     * @return array<string, mixed>
     */
    private function formatPaginator(LengthAwarePaginator $paginator, callable $map): array
    {
        return [
            'data' => $paginator->getCollection()->map($map)->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
                'last' => $paginator->url($paginator->lastPage()),
            ],
        ];
    }
}

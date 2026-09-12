<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasPeriodFilter
{
    /**
     * Calcule la plage de dates [start, end] et le libellé pour la période demandée.
     *
     * @param string|null $period  jour, semaine, mois, trimestre, semestre, annuel, custom, tous
     * @param string|null $customStart
     * @param string|null $customEnd
     * @return array [ ?Carbon $start, ?Carbon $end, string $label, string $period ]
     */
    public function getPeriodDates(mixed $period = 'tous', mixed $customStart = null, mixed $customEnd = null): array
    {
        if (is_array($period)) {
            $period = reset($period);
        }
        if (is_array($customStart)) {
            $customStart = reset($customStart);
        }
        if (is_array($customEnd)) {
            $customEnd = reset($customEnd);
        }

        $now = Carbon::now();
        $start = null;
        $end = null;

        $periodStr = strtolower((string) ($period ?? 'tous'));

        switch ($periodStr) {
            case 'jour':
                $start = Carbon::today();
                $end = Carbon::today()->endOfDay();
                $label = "Aujourd'hui (" . $start->format('d/m/Y') . ")";
                break;

            case 'semaine':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $label = "Cette semaine (du " . $start->format('d/m') . " au " . $end->format('d/m/Y') . ")";
                break;

            case 'mois':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $label = "Ce mois (" . $start->locale('fr')->translatedFormat('F Y') . ")";
                break;

            case 'trimestre':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                $quarter = ceil($now->month / 3);
                $label = "T" . $quarter . " " . $now->year . " (" . $start->format('d/m') . " au " . $end->format('d/m/Y') . ")";
                break;

            case 'semestre':
                if ($now->month <= 6) {
                    $start = Carbon::create($now->year, 1, 1)->startOfDay();
                    $end = Carbon::create($now->year, 6, 30)->endOfDay();
                    $label = "1er Semestre " . $now->year . " (01/01 au 30/06)";
                } else {
                    $start = Carbon::create($now->year, 7, 1)->startOfDay();
                    $end = Carbon::create($now->year, 12, 31)->endOfDay();
                    $label = "2nd Semestre " . $now->year . " (01/07 au 31/12)";
                }
                break;

            case 'annuel':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $label = "Année " . $now->year;
                break;

            case 'custom':
                if ($customStart) {
                    $start = Carbon::parse((string) $customStart)->startOfDay();
                }
                if ($customEnd) {
                    $end = Carbon::parse((string) $customEnd)->endOfDay();
                }
                if ($start && $end) {
                    $label = "Du " . $start->format('d/m/Y') . " au " . $end->format('d/m/Y');
                } elseif ($start) {
                    $label = "À partir du " . $start->format('d/m/Y');
                } elseif ($end) {
                    $label = "Jusqu'au " . $end->format('d/m/Y');
                } else {
                    $label = "Période personnalisée";
                }
                break;

            case 'tous':
            default:
                $periodStr = 'tous';
                $label = "Toutes les périodes (Historique complet)";
                break;
        }

        return [
            'start' => $start,
            'end' => $end,
            'label' => $label,
            'period' => $periodStr,
        ];
    }

    /**
     * Applique un filtre de date sur une requête Eloquent si $start et $end sont fournis.
     */
    public function applyDateFilter($query, ?Carbon $start, ?Carbon $end, string $column = 'created_at')
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        } elseif ($start) {
            $query->where($column, '>=', $start);
        } elseif ($end) {
            $query->where($column, '<=', $end);
        }

        return $query;
    }

    public function applySearchFilter($query, mixed $keyword, array $columns)
    {
        if (is_array($keyword)) {
            $keyword = implode(' ', array_filter($keyword, fn($item) => is_string($item) || is_numeric($item)));
        }

        $keywordStr = trim((string) ($keyword ?? ''));
        if (!empty($keywordStr)) {
            $query->where(function ($q) use ($keywordStr, $columns) {
                foreach ($columns as $column) {
                    if (str_contains($column, '.')) {
                        $parts = explode('.', $column);
                        $field = array_pop($parts);
                        $relationPath = implode('.', $parts);

                        $q->orWhereHas($relationPath, function ($rq) use ($keywordStr, $field) {
                            $rq->where($field, 'LIKE', "%{$keywordStr}%");
                        });
                    } else {
                        $q->orWhere($column, 'LIKE', "%{$keywordStr}%");
                    }
                }
            });
        }

        return $query;
    }
}

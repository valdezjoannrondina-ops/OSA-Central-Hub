@php
    // Show selected month and next month for minimized view
    $currentMonth = $selectedMonth->copy();
    $nextMonth = $selectedMonth->copy()->addMonth();
    $monthsToShow = [$currentMonth, $nextMonth];
@endphp
@foreach($monthsToShow as $month)
    @php
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd = $month->copy()->endOfMonth();
        $startCell = $monthStart->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endCell = $monthEnd->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
    @endphp
    <div class="col-md-6 mb-3">
        <div class="card border">
            <div class="card-header py-2" style="background-color: #e9ecef; color: #000;">
                <strong class="text-small">{{ $month->format('F Y') }}</strong>
            </div>
            <div class="card-body p-2">
                <table class="table table-bordered table-sm mb-0" style="font-size: 0.7rem;">
                    <thead>
                        <tr class="text-center">
                            <th class="p-1" style="width: 14.28%;">S</th>
                            <th class="p-1" style="width: 14.28%;">M</th>
                            <th class="p-1" style="width: 14.28%;">T</th>
                            <th class="p-1" style="width: 14.28%;">W</th>
                            <th class="p-1" style="width: 14.28%;">T</th>
                            <th class="p-1" style="width: 14.28%;">F</th>
                            <th class="p-1" style="width: 14.28%;">S</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($date = $startCell->copy(); $date->lte($endCell);)
                            <tr>
                                @for($i = 0; $i < 7; $i++)
                                    @php
                                        $isInMonth = $date->between($monthStart, $monthEnd);
                                        $isToday = $date->isToday();
                                        $dateKey = $date->format('Y-m-d');
                                        $dayEvents = $eventsByDate->get($dateKey, collect());
                                        $hasEvent = $dayEvents->isNotEmpty();
                                    @endphp
                                    <td class="p-1 text-center align-middle {{ $isInMonth ? 'in-month' : 'out-month' }}" 
                                        style="height: 35px; cursor: default; {{ !$isInMonth ? 'background-color: #f8f9fa;' : '' }} {{ $isToday ? 'background-color: #fff3cd;' : '' }}">
                                        <div class="d-flex flex-column h-100 justify-content-between">
                                            <div class="date-number text-{{ !$isInMonth ? 'muted' : ($hasEvent ? 'danger fw-bold' : ($isToday ? 'primary' : 'dark')) }}" style="font-size: 0.7rem;">
                                                {{ $date->day }}
                                            </div>
                                            @if($isInMonth && $hasEvent)
                                                <div class="text-center" style="font-size: 0.6rem;">
                                                    <span class="badge bg-danger" style="font-size: 0.5rem;">●</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    @php $date->addDay(); @endphp
                                @endfor
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach


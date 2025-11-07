<div class="page-section" id="upcoming-events">
    <div class="container">
        <h1 class="text-center mb-5 wow fadeInUp">Upcoming Events and Calendar of Activities</h1>

        <div class="row g-4">
            <!-- Staff Events Section -->
            <div class="col-12">
                <div class="bg-white p-3 shadow-sm rounded h-100">
                    <h5 class="mb-3">Upcoming Events (Staff)</h5>
                    @forelse(($staffEvents ?? []) as $ev)
                        <div class="event-card border rounded p-3 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $ev->name }}</strong>
                                <span class="badge">{{ \Carbon\Carbon::parse($ev->event_date)->format('M d, Y') }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                @if($ev->start_time && $ev->end_time)
                                    🕒 {{ \Carbon\Carbon::parse($ev->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($ev->end_time)->format('g:i A') }}
                                @elseif($ev->start_time)
                                    🕒 {{ \Carbon\Carbon::parse($ev->start_time)->format('g:i A') }}
                                @endif
                                @if($ev->location)
                                    &nbsp; • 📍 {{ $ev->location }}
                                @endif
                            </div>
                            @if($ev->description)
                                <div class="mt-2 text-secondary" style="white-space: pre-line;">{{ \Illuminate\Support\Str::limit($ev->description, 160) }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-muted">No upcoming events from staff.</div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Academic Calendar Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="bg-white p-3 shadow-sm rounded">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0"><strong>Academic Calendar AY {{ $calendarYear ?? now()->year }}-{{ ($calendarYear ?? now()->year) + 1 }}</strong></h5>
                        <div class="d-flex gap-2">
                            <a href="{{ url('/').'?year='.(($calendarYear ?? now()->year) - 1) }}#upcoming-events" class="btn btn-sm btn-outline-secondary">&laquo; Previous Year</a>
                            <a href="{{ url('/').'?year='.(($calendarYear ?? now()->year) + 1) }}#upcoming-events" class="btn btn-sm btn-outline-secondary">Next Year &raquo;</a>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap" style="gap: 5px;">
                        @php
                            $year = $calendarYear ?? now()->year;
                            $monthColors = ['August' => 'danger', 'September' => 'success', 'October' => 'danger', 
                                           'November' => 'success', 'December' => 'info', 'January' => 'info',
                                           'February' => 'info', 'March' => 'success', 'April' => 'success', 
                                           'May' => 'success', 'June' => 'warning', 'July' => 'primary'];
                            $monthList = $months ?? ['August', 'September', 'October', 'November', 'December', 'January', 
                                      'February', 'March', 'April', 'May', 'June'];
                            $currentIndex = $currentMonthIndex ?? 0;
                            $monthName = $monthList[$currentIndex] ?? 'August';
                            
                            // Calculate previous and next month indices
                            $prevIndex = $currentIndex > 0 ? $currentIndex - 1 : count($monthList) - 1;
                            $nextIndex = $currentIndex < count($monthList) - 1 ? $currentIndex + 1 : 0;
                            $prevMonth = $monthList[$prevIndex];
                            $nextMonth = $monthList[$nextIndex];
                            
                            $monthNum = \Carbon\Carbon::createFromFormat('F', $monthName)->format('n');
                            $displayYear = in_array($monthName, ['August', 'September', 'October', 'November', 'December']) ? $year : ($year + 1);
                            $month = \Carbon\Carbon::createFromDate($displayYear, $monthNum, 1);
                            $monthStart = $month->copy()->startOfMonth();
                            $monthEnd = $month->copy()->endOfMonth();
                            $startCell = $monthStart->copy()->startOfWeek(\Carbon\Carbon::SUNDAY);
                            $endCell = $monthEnd->copy()->endOfWeek(\Carbon\Carbon::SATURDAY);
                            $colorClass = $monthColors[$monthName] ?? 'secondary';
                            $classDays = 0;
                        @endphp
                        
                        <!-- Calendar -->
                        <div class="calendar-container" style="flex: 1 1 25%; min-width: 200px;">
                            <div class="card">
                                <div class="card-header bg-{{ $colorClass }} text-white d-flex justify-content-between align-items-center" style="padding: 0.5rem;">
                                    <strong style="font-size: 0.85rem;">{{ $monthName }} {{ $displayYear }}</strong>
                                    <div class="d-flex gap-1">
                                        <a href="{{ url('/').'?year='.$year.'&cal_month='.$prevMonth.'-'.$displayYear }}#upcoming-events" class="btn btn-sm btn-light" style="padding: 0.25rem 0.5rem;" title="Previous Month">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                        <a href="{{ url('/').'?year='.$year.'&cal_month='.$nextMonth.'-'.$displayYear }}#upcoming-events" class="btn btn-sm btn-light" style="padding: 0.25rem 0.5rem;" title="Next Month">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-1">
                                    <table class="table table-bordered table-sm mb-1 calendar-small-view" style="font-size: 0.55rem;">
                                        <thead>
                                            <tr class="text-center">
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Sun</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Mon</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Tue</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Wed</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Thu</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Fri</th>
                                                <th class="p-0" style="width: 14.28%; font-size: 0.5rem;">Sat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @for($date = $startCell->copy(); $date->lte($endCell);)
                                                <tr>
                                                    @for($i = 0; $i < 7; $i++)
                                                        @php
                                                            $isInMonth = $date->between($monthStart, $monthEnd);
                                                            $isWeekend = in_array($date->dayOfWeek, [0, 6]);
                                                            $dateKey = $date->format('Y-m-d');
                                                            $dayEvents = ($eventsByDate ?? collect())->get($dateKey, collect());
                                                            $isHoliday = $dayEvents->isNotEmpty();
                                                            if ($isInMonth && !$isWeekend && !$isHoliday) {
                                                                $classDays++;
                                                            }
                                                        @endphp
                                                        <td class="p-0 text-center align-middle calendar-cell {{ $isInMonth ? 'in-month' : 'out-month' }}" 
                                                            style="height: 28px; {{ !$isInMonth ? 'background-color: #f8f9fa;' : '' }}">
                                                            <div class="d-flex flex-column h-100 justify-content-between">
                                                                <div class="date-number text-{{ !$isInMonth ? 'muted' : ($isHoliday ? 'danger fw-bold' : 'dark') }}" style="font-size: 0.6rem;">
                                                                    {{ $date->day }}
                                                                </div>
                                                                @if($isInMonth)
                                                                    <div class="date-marker" style="font-size: 0.45rem; line-height: 1; min-height: 6px;">
                                                                        @if($isWeekend && !$isHoliday)
                                                                            <span class="text-danger fw-bold">X</span>
                                                                        @elseif($isHoliday)
                                                                            <span class="text-danger fw-bold" title="{{ $dayEvents->pluck('name')->implode(', ') }}">
                                                                                @if($dayEvents->count() == 1)
                                                                                    {{ \Illuminate\Support\Str::limit($dayEvents->first()->name, 4) }}
                                                                                @else
                                                                                    {{ $dayEvents->count() }}
                                                                                @endif
                                                                            </span>
                                                                        @endif
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
                                    <div class="text-center mt-1">
                                        <strong style="font-size: 0.65rem;">{{ $monthName }} {{ $displayYear }} Class Days: {{ $classDays }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Semester Dates -->
                        <div class="semester-container" style="flex: 1 1 20%; min-width: 180px;">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white" style="padding: 0.5rem;">
                                    <strong style="font-size: 0.85rem;">Semester Dates</strong>
                                </div>
                                <div class="card-body" style="padding: 0.75rem; font-size: 0.75rem;">
                                    <p style="margin-bottom: 0.5rem;"><strong>First Semester {{ $year }}-{{ $year + 1 }}:</strong><br>
                                    <small>August 31, {{ $year }} - January 24, {{ $year + 1 }}</small></p>
                                    
                                    <p style="margin-bottom: 0.5rem;"><strong>Second Semester {{ $year }}-{{ $year + 1 }}:</strong><br>
                                    <small>February 2, {{ $year + 1 }} - June 18, {{ $year + 1 }}</small></p>
                                    
                                    <p style="margin-bottom: 0;"><strong>Mid-Year Term {{ $year + 1 }}:</strong><br>
                                    <small>July 1, {{ $year + 1 }} - August 10, {{ $year + 1 }} (6 Weeks)</small></p>
                                </div>
                            </div>
                        </div>

                        <!-- Holidays -->
                        <div class="holidays-container" style="flex: 1 1 30%; min-width: 220px;">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark" style="padding: 0.5rem;">
                                    <strong style="font-size: 0.85rem;">Holidays {{ $year }}</strong>
                                </div>
                                <div class="card-body" style="max-height: 400px; overflow-y: auto; padding: 0.75rem; font-size: 0.7rem;">
                                    @php
                                        // Filter holidays: Same logic as admin/calendar page Holidays section
                                        // Show ALL events for the year (same as admin calendar shows)
                                        $holidayEvents = ($calendarEvents ?? collect())->filter(function($e) use ($year) {
                                            // Use start_time (same as admin calendar)
                                            $eventDate = $e->start_time ?? $e->event_date;
                                            $eventYear = \Carbon\Carbon::parse($eventDate)->year;
                                            
                                            // Filter for year only (same as admin calendar Holidays section)
                                            return $eventYear == $year || $eventYear == $year + 1;
                                        })->sortBy(function($e) {
                                            // Use start_time (same as admin calendar)
                                            return \Carbon\Carbon::parse($e->start_time ?? $e->event_date)->timestamp;
                                        });
                                        
                                        $eventsByMonth = $holidayEvents->groupBy(function($e) {
                                            // Use start_time (same as admin calendar)
                                            return \Carbon\Carbon::parse($e->start_time ?? $e->event_date)->format('F Y');
                                        });
                                    @endphp
                                    
                                    @foreach($eventsByMonth as $monthYear => $monthEvents)
                                        <div class="mb-2">
                                            <strong style="font-size: 0.75rem;">{{ $monthYear }}</strong>
                                            <ul class="list-unstyled ms-2" style="margin-bottom: 0.5rem;">
                                                @foreach($monthEvents as $ev)
                                                    <li class="text-danger" style="font-size: 0.7rem;">
                                                        <strong>{{ \Carbon\Carbon::parse($ev->start_time ?? $ev->event_date)->format('M d') }}:</strong> 
                                                        {{ $ev->name }}
                                                        @if($ev->description)
                                                            <br><small class="text-muted" style="font-size: 0.65rem;">({{ $ev->description }})</small>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                    
                                    @if($holidayEvents->isEmpty())
                                        <p class="text-muted" style="font-size: 0.7rem;">No events scheduled for this academic year.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Summary of School Days -->
                        <div class="summary-container" style="flex: 1 1 20%; min-width: 180px;">
                            <div class="card h-100">
                                <div class="card-header bg-info text-white" style="padding: 0.5rem;">
                                    <strong style="font-size: 0.85rem;">Summary of School Days</strong>
                                </div>
                                <div class="card-body" style="padding: 0.75rem; font-size: 0.75rem;">
                                    @php
                                        $firstSemTotal = 0;
                                        $secondSemTotal = 0;
                                        foreach(['August', 'September', 'October', 'November', 'December', 'January'] as $m) {
                                            $mNum = \Carbon\Carbon::createFromFormat('F', $m)->format('n');
                                            $mYear = in_array($m, ['August', 'September', 'October', 'November', 'December']) ? $year : ($year + 1);
                                            $mStart = \Carbon\Carbon::createFromDate($mYear, $mNum, 1)->startOfMonth();
                                            $mEnd = \Carbon\Carbon::createFromDate($mYear, $mNum, 1)->endOfMonth();
                                            $days = 0;
                                            for($d = $mStart->copy(); $d->lte($mEnd); $d->addDay()) {
                                                if (!in_array($d->dayOfWeek, [0,6])) {
                                                    $dKey = $d->format('Y-m-d');
                                                    if (!($eventsByDate ?? collect())->has($dKey)) $days++;
                                                }
                                            }
                                            $firstSemTotal += $days;
                                        }
                                        foreach(['February', 'March', 'April', 'May', 'June'] as $m) {
                                            $mNum = \Carbon\Carbon::createFromFormat('F', $m)->format('n');
                                            $mYear = $year + 1;
                                            $mStart = \Carbon\Carbon::createFromDate($mYear, $mNum, 1)->startOfMonth();
                                            $mEnd = \Carbon\Carbon::createFromDate($mYear, $mNum, 1)->endOfMonth();
                                            $days = 0;
                                            for($d = $mStart->copy(); $d->lte($mEnd); $d->addDay()) {
                                                if (!in_array($d->dayOfWeek, [0,6])) {
                                                    $dKey = $d->format('Y-m-d');
                                                    if (!($eventsByDate ?? collect())->has($dKey)) $days++;
                                                }
                                            }
                                            $secondSemTotal += $days;
                                        }
                                    @endphp
                                    <p style="margin-bottom: 0.5rem;"><strong>First Semester Total:</strong> {{ $firstSemTotal }}</p>
                                    <p style="margin-bottom: 0.5rem;"><strong>Second Semester Total:</strong> {{ $secondSemTotal }}</p>
                                    <p style="margin-bottom: 0;"><strong>Total:</strong> {{ $firstSemTotal + $secondSemTotal }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <style>
            .calendar-cell.in-month {
                cursor: default;
            }
            .text-danger {
                color: #dc3545 !important;
            }
            .date-marker {
                font-size: 0.65rem;
            }
            .calendar-small-view {
                font-size: 0.55rem !important;
            }
            .calendar-small-view td {
                padding: 0.05rem !important;
                height: 28px !important;
                vertical-align: middle !important;
            }
            .calendar-small-view th {
                padding: 0.05rem !important;
                font-size: 0.5rem !important;
            }
            .calendar-small-view .date-number {
                font-size: 0.6rem !important;
            }
            .calendar-small-view .date-marker {
                font-size: 0.45rem !important;
                line-height: 1 !important;
            }
            .calendar-small-view .card-body {
                padding: 0.4rem !important;
            }
        </style>
    </div>
</div>

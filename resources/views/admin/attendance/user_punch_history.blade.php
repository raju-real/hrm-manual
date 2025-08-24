<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Punch History
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr class="text-nowrap">
                                <th class="text-center">Sl.no</th>
                                <th>Client IP</th>
                                <th class="text-center">Check In</th>
                                <th class="text-center">Check Out</th>
                                <th class="text-center">Working Hour</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($punch_history as $attendance)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $attendance->client_ip ?? '' }}</td>
                                    <td class="text-center">{{ timeFormat($attendance->check_in, 'h:i A') }}</td>
                                    <td class="text-center">
                                        {{ $attendance->check_out ? timeFormat($attendance->check_out, 'h:i A') : '-' }}
                                    </td>
                                    <td class="text-center">{{ $attendance->minute_in_hour ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <x-no-data-found />
                                    </td>
                                </tr>
                            @endforelse

                            @if ($punch_history->isNotEmpty())
                                <tr class="fw-bold bg-light">
                                    <td colspan="4" class="text-end">Total Hours</td>
                                    <td class="text-center">{{ $totalHours }}</td>
                                </tr>
                            @endif
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

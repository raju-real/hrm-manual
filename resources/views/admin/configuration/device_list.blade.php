@extends('admin.layouts.app')
@section('title', 'Device List')
@push('css')
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Device List</h4>
                <div class="page-title-right">
                    <a href="{{ route('admin.devices.create') }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus-circle"></i> Add New
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered mb-0 text-nowrap">
                            <thead>
                                <tr>
                                    <th class="text-center">Sl.no</th>
                                    <th>Name</th>
                                    <th>Branch</th>
                                    <th>Serial No</th>
                                    <th>IP</th>
                                    <th class="text-center">Enabled</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($devices as $device)
                                    <tr>
                                        <td class="text-center">{{ $loop->index + 1 }}</td>
                                        <td>{{ $device->name ?? '' }}</td>
                                        <td>{{ $device->branch->name ?? '' }}</td>
                                        <td>{{ $device->serial_no ?? '' }}</td>
                                        <td>{{ $device->ip ?? '' }}</td>
                                        <td class="text-center">
                                            <input type="checkbox" id="device-{{ $loop->index + 1 }}" class="device-status"
                                                data-id="{{ $device->id }}" switch="bool"
                                                {{ isActive($device->status) ? 'checked' : '' }} />
                                            <label class="custom-label-margin" for="device-{{ $loop->index + 1 }}"
                                                data-on-label="Yes" data-off-label="No"></label>
                                        </td>
                                        <td class="text-center">
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"
                                                href="{{ route('admin.devices.edit', $device->slug) }}"
                                                class="btn btn-sm btn-soft-success"><i class="fa fa-edit"></i></a>
                                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"
                                                class="btn btn-sm btn-soft-danger delete-data"
                                                data-id="{{ 'delete-device-' . $device->id }}" href="javascript:void(0);">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <form id="delete-device-{{ $device->id }}"
                                                action="{{ route('admin.devices.destroy', $device->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <x-no-data-found></x-no-data-found>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="d-flex justify-content-center">
                    {!! $devices->links('pagination::bootstrap-4') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/admin/js/custom/device_list.js') }}"></script>
@endpush

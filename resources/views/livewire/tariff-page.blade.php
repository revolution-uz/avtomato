@php /** @var \App\Models\Company\Tariffs[] $tariffs */ @endphp
<div>
    @section('title', 'Tariffs')
    @section('vendor-style')
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    @endsection
    <h4 class="fw-semibold mb-4">Tariffs</h4>

    <p class="mb-4">On this page you can add new Tariffs or edit/delete created tariffs.</p>

    <div class="modal fade show" tabindex="-1" style="display: {{ $this->isFormVisible ? 'block' : 'none' }};">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" aria-label="Close" wire:click="resetForm"></button>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="mb-2">{{ $this->tariff_id ? 'Update' : 'Create' }} </h3>
                        <p class="text-muted">By creating tariffs, you can divide your documents into tariffs.</p>
                    </div>
                    <form class="row fv-plugins-bootstrap5 fv-plugins-framework" wire:submit="createOrUpdate">
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="name">Tariff Name</label>
                            <input type="text" id="name" wire:model="name" class="form-control" placeholder="Enter tariff name">
                            @error('name') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="inn">Is Annual</label>
                            <input type="checkbox" id="is_annual" wire:model="is_annual" class="form-control" placeholder="Is Annual">
                            @error('is_annual') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Percent</label>
                            <input type="text" id="percent" wire:model="percent" class="form-control" placeholder="Enter tariff percent">
                            @error('percent') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Min Amount</label>
                            <input type="text" id="min_amount" wire:model="min_amount" class="form-control" placeholder="Enter tariff min amount">
                            @error('min_amount') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Max Amount</label>
                            <input type="text" id="max_amount" wire:model="max_amount" class="form-control" placeholder="Enter tariff max amount">
                            @error('max_amount') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Min initial fee</label>
                            <input type="text" id="min_initial_fee" wire:model="min_initial_fee" class="form-control" placeholder="Enter tariff min initial fee">
                            @error('min_initial_fee') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Max initial fee</label>
                            <input type="text" id="max_initial_fee" wire:model="max_initial_fee" class="form-control" placeholder="Enter tariff max initial fee">
                            @error('max_initial_fee') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 text-center demo-vertical-spacing">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light">{{ $this->tariff_id ? 'Update' : 'Create' }} tariff</button>
                            <button type="reset" class="btn btn-label-secondary waves-effect" wire:click="resetForm">Discard</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Permission Table -->
    <div class="card">
        <div class="card-datatable table-responsive">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <div class="row mx-1">
                    <div class="col-sm-12 col-md-3">
                        <div class="dataTables_length">
                            <label>
                                Show
                                <select class="form-select" wire:model.live="perPage">
                                    @foreach(\App\Constants\PaginatorConstants::PER_PAGE_OPTIONS as $perPageOption)
                                        <option value="{{$perPageOption}}">{{$perPageOption}}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9">
                        <div
                            class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1">
                            <div class="me-3">
                                <div class="dataTables_filter">
                                    <label>
                                        Search
                                        <input wire:model.live.debounce.500ms="term" type="search" class="form-control" placeholder="Search.."/>
                                    </label>
                                </div>
                            </div>
                            <div class="dt-buttons btn-group flex-wrap">
                                <button class="btn add-new btn-primary mb-3 mb-md-0" tabindex="0"
                                        wire:click="showForm">
                                    <span>Add tariff</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="datatables-permissions table border-top dataTable no-footer dtr-column">
                    <thead>
                    <tr>
                        <th class="sorting">Name</th>
                        <th class="sorting_disabled">Is annual</th>
                        <th class="sorting_disabled">Percent</th>
                        <th class="sorting_disabled">Min amount</th>
                        <th class="sorting_disabled">Max amount</th>
                        <th class="sorting_disabled">Min initial fee</th>
                        <th class="sorting_disabled">Max initial fee</th>
                        <th class="sorting_disabled">Created Date</th>
                        <th class="sorting_disabled">Updated Date</th>
                        <th class="sorting_disabled">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($tariffs as $counter => $tariff)
                    <tr class="{{ $counter % 2 ? 'odd' : 'even' }}">
                        <td><span class="text-nowrap">{{ $tariff->name }}</span></td>
                        <td><span class="text-nowrap">{{ $tariff->is_annual }}</td>
                        <td><span class="text-nowrap">{{ $tariff->percent }}</td>
                        <td><span class="text-nowrap">{{ $tariff->min_amount }}</td>
                        <td><span class="text-nowrap">{{ $tariff->max_amount }}</td>
                        <td><span class="text-nowrap">{{ $tariff->min_initial_fee }}</td>
                        <td><span class="text-nowrap">{{ $tariff->max_initial_fee }}</td>
                        <td><span class="text-nowrap">{{ $tariff->created_at }}</td>
                        <td><span class="text-nowrap">{{ $tariff->updated_at }}</td>
                        <td>
                            <span class="text-nowrap">
                                <button class="btn btn-sm btn-icon me-2" wire:click="showForm({{$tariff->id}})">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-icon delete-record">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $tariffs->links('livewire.partials.pagination') }}
            </div>
        </div>
    </div>
    <!--/ Permission Table -->
</div>

@php /** @var \App\Models\Company\Branch[] $branches */ @endphp
<div>
    @section('title', 'Branches')
    @section('vendor-style')
        <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    @endsection
    <h4 class="fw-semibold mb-4">Branches</h4>

    <p class="mb-4">On this page you can add new branches or edit/delete created branches.</p>

    <div class="modal fade show" tabindex="-1" style="display: {{ $this->isFormVisible ? 'block' : 'none' }};">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <button type="button" class="btn-close btn-pinned" aria-label="Close" wire:click="resetForm"></button>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h3 class="mb-2">{{ $this->branch_id ? 'Update' : 'Create' }} </h3>
                        <p class="text-muted">By creating branches, you can divide your documents into branches.</p>
                    </div>
                    <form class="row fv-plugins-bootstrap5 fv-plugins-framework" wire:submit="createOrUpdate">
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="name">Branch Name</label>
                            <input type="text" id="name" wire:model="name" class="form-control" placeholder="Enter branch name">
                            @error('name') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="inn">Branch INN</label>
                            <input type="text" id="inn" wire:model="inn" class="form-control" placeholder="Enter branch INN number">
                            @error('inn') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 mb-3 fv-plugins-icon-container">
                            <label class="form-label" for="address">Branch Address</label>
                            <input type="text" id="address" wire:model="address" class="form-control" placeholder="Enter branch address">
                            @error('address') <div class="fv-plugins-message-container invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12 text-center demo-vertical-spacing">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 waves-effect waves-light">{{ $this->branch_id ? 'Update' : 'Create' }} Branch</button>
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
                                    <span>Add Branch</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="datatables-permissions table border-top dataTable no-footer dtr-column">
                    <thead>
                    <tr>
                        <th class="sorting">Name</th>
                        <th class="sorting_disabled">INN</th>
                        <th class="sorting_disabled">Address</th>
                        <th class="sorting_disabled">Created Date</th>
                        <th class="sorting_disabled">Updated Date</th>
                        <th class="sorting_disabled">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($branches as $counter => $branch)
                    <tr class="{{ $counter % 2 ? 'odd' : 'even' }}">
                        <td><span class="text-nowrap">{{ $branch->name }}</span></td>
                        <td><span class="text-nowrap">{{ $branch->inn }}</td>
                        <td><span class="text-nowrap">{{ $branch->address }}</td>
                        <td><span class="text-nowrap">{{ $branch->created_at }}</td>
                        <td><span class="text-nowrap">{{ $branch->updated_at }}</td>
                        <td>
                            <span class="text-nowrap">
                                <button class="btn btn-sm btn-icon me-2" wire:click="showForm({{$branch->id}})">
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
                {{ $branches->links('livewire.partials.pagination') }}
            </div>
        </div>
    </div>
    <!--/ Permission Table -->
</div>

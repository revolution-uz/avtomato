<div>
    @section('title', 'Calculator')
    @section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
    @endsection

    @section('vendor-script')
    <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    @endsection

    @section('page-script')
    <script src="{{asset('assets/js/tables-datatables-basic.js')}}"></script>
    @endsection
    <h4 class="fw-semibold mb-4">Calculator</h4>
    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <table class="datatables-basic table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Portion</th>
                    <th>Monthly</th>
                </tr>
                </thead>
                <tbody>
                @foreach($calculated as $item)
                <tr>
                    <td>{{$item['id'];}}</td>
                    <td>{{$item['date'];}}</td>
                    <td>{{$item['amount'];}}</td>
                    <td>{{$item['portion'];}}</td>
                    <td>{{$item['monthly'];}}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

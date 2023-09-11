<div class="modal fade" id="pricingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-simple modal-pricing">
        <div class="modal-content p-2 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="card">
                    <div class="card-datatable table-responsive pt-0">
                        <table class="datatables-basic table">
                            <thead>
                            <tr>
                                <th>#</th>
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
                                <td>{{$item['amount'];}}</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@extends('layouts.master')

@section('title')
    Create Transaction
@endsection

@push('css')
    <style>
        .show-final_price {
            font-size: 5em;
            text-align: center;
            height: 100px;
        }

        .show-in_word {
            padding: 10px;
            background: #f0f0f0;
        }

        .table-transaction_detail tbody tr:last-child {
            display: none;
        }

        @media(max-width: 768px) {
            .show-final_price {
                font-size: 3em;
                height: 70px;
                padding-top: 5px;
            }
        }
    </style>
@endpush

@section('breadcrumb')
    @parent
    <li class="active">Create Transaction</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-body">

                    <form class="form-product">
                        @csrf
                        <div class="form-group row">
                            <label for="code" class="col-lg-2">Transaction detail</label>
                            <div class="col-lg-5">
                                <div class="input-group">
                                    <input type="hidden" name="transaction_id" id="transaction_id" value="{{ $transaction_id }}">
                                    <input type="hidden" name="product_id" id="product_id">
                                    <input type="text" class="form-control" name="code" id="code">
                                    <span class="input-group-btn">
                                    <button onclick="showProductModal()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                </span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <table class="table table-stiped table-bordered table-transaction_detail">
                        <thead>
                        <th width="10px">No</th>
                        <th>Product Code</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th width="5%">Quantity</th>
                        <th>Subtotal</th>
                        <th width="5%"><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>

                    <div class="row">
                        <div class="col-lg-8">
                            <div class="show-final_price bg-primary"></div>
                            <div class="show-in_word"></div>
                        </div>
                        <div class="col-lg-4">
                            <form action="{{ route('transaction.save') }}" class="form-transaction_detail" method="post">
                                @csrf
                                <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                                <input type="hidden" name="total_price" id="total_price">
                                <input type="hidden" name="total_item_quantity" id="total_item_quantity">
                                <input type="hidden" name="final_price" id="final_price">
                                <input type="hidden" name="guest_id" id="guest_id" value="{{ $guestSelected->guest_id }}">

                                <div class="form-group row">
                                    <label for="totalrp" class="col-lg-2 control-label">Total</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="totalrp" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="name" class="col-lg-2 control-label">Member</label>
                                    <div class="col-lg-8">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="name" value="{{ $guestSelected->name }}">
                                            <span class="input-group-btn">
                                            <button onclick="showGuestModal()" class="btn btn-info btn-flat" type="button"><i class="fa fa-arrow-right"></i></button>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="final_price" class="col-lg-2 control-label">Bayar</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="final_pricerp" class="form-control" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="money_received" class="col-lg-2 control-label">Diterima</label>
                                    <div class="col-lg-8">
                                        <input type="number" id="money_received" class="form-control" name="money_received" value="{{ $transaction->money_received ?? 0 }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="changerp" class="col-lg-2 control-label">Change</label>
                                    <div class="col-lg-8">
                                        <input type="text" id="changerp" name="changerp" class="form-control" value="0" readonly>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary btn-sm btn-flat pull-right btn-save"><i class="fa fa-floppy-o"></i> Create Transaction</button>
                </div>
            </div>
        </div>
    </div>

    @includeIf('transaction_detail.product')
    @includeIf('transaction_detail.guest')
@endsection

@push('scripts')
    <script>
        let table, table2;

        $(function () {
            $('body').addClass('sidebar-collapse');

            table = $('.table-transaction_detail').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('transaction_detail.data', $transaction_id) }}',
                },
                columns: [
                    {data: 'DT_RowIndex', searchable: false, sortable: false},
                    {data: 'code'},
                    {data: 'name'},
                    {data: 'sell_price'},
                    {data: 'quantity'},
                    {data: 'price'},
                    {data: 'action', searchable: false, sortable: false},
                ],
                dom: 'Brt',
                bSort: false,
                paginate: false
            })
                .on('draw.dt', function () {
                    loadForm();
                    setTimeout(() => {
                        $('#money_received').trigger('input');
                    }, 300);
                });
            table2 = $('.table-product').DataTable();

            $(document).on('input', '.quantity', function () {
                let id = $(this).data('id');
                let product_id = $(this).data('product_id');
                let quantity = parseInt($(this).val());

                if (isNaN(quantity)) {
                    $(this).val(1);
                    alert('Quantity cannot be less than 1');
                    quantity = 1;
                }
                if (isNaN(quantity) || quantity < 1) {
                    $(this).val(1);
                    alert('Quantity cannot be less than 1');
                    quantity = 1;
                }
                if (quantity > 10000) {
                    $(this).val(10000);
                    alert('Quantity cannot be more than 10000');
                    quantity = 10000;
                }

                $.get(`{{ url('/product/${product_id}') }}`)
                    .done((response) => {
                        if (quantity > response.stock) {
                            $(this).val(response.stock);
                            alert('Quantity cannot be more than available stock');
                            quantity = response.stock;
                        }

                        $.post(`{{ url('/transaction_detail') }}/${id}`, {
                            '_token': $('[name=csrf-token]').attr('content'),
                            '_method': 'put',
                            'quantity': quantity
                        })
                            .done(response => {
                                table.ajax.reload(() => loadForm());
                            })
                            .fail((errors) => {
                                alert(errors.responseText);
                            });
                    })
                    .fail(() => {
                        showToast('error', errors.responseText || 'Failed to load data');
                    });
            });

            $('#money_received').on('input', function () {
                if ($(this).val() == "") {
                    $(this).val(0).select();
                }

                loadForm($(this).val());
            }).focus(function () {
                $(this).select();
            });

            $('.btn-save').on('click', function () {
                var guestIdField = document.getElementById('guest_id');
                var transactionTable = document.querySelector('.table-transaction_detail tbody');

                if (guestIdField.value.trim() === '') {
                    alert('Please input member');
                    event.preventDefault();
                    return
                }

                if (transactionTable.rows.length <= 1) {
                    alert('Please input atleast 1 product');
                    event.preventDefault();
                    return
                }

                $('.form-transaction_detail').submit();
            });
        });

        function showProductModal() {
            $('#modal-product').modal('show');
        }

        function hideProductModal() {
            $('#modal-product').modal('hide');
        }

        function chooseProduct(id, kode) {
            $('#product_id').val(id);
            $('#code').val(kode);
            hideProductModal();
            addProductToTransaction();
        }

        function addProductToTransaction() {
            $.post('{{ route('transaction_detail.store') }}', $('.form-product').serialize())
                .done(response => {
                    $('#code').focus();
                    table.ajax.reload(() => loadForm());
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || 'Failed to load data');
                });
        }

        function showGuestModal() {
            $('#modal-guest').modal('show');
        }

        function hideGuestModal() {
            $('#modal-guest').modal('hide');
        }

        function chooseGuest(id, name) {
            $('#guest_id').val(id);
            $('#name').val(name);
            loadForm();
            $('#money_received').val(0).focus().select();
            hideGuestModal();
        }

        function deleteData(url) {
            if (confirm('Yakin ingin menghapus data terpilih?')) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                    .done((response) => {
                        table.ajax.reload(() => loadForm());
                    })
                    .fail((errors) => {
                        alert('Tidak dapat menghapus data');
                        return;
                    });
            }
        }

        function loadForm(money_received = 0) {
            $('#total_price').val($('.total_price').text());
            $('#total_item_quantity').val($('.total_item_quantity').text());

            $.get(`{{ url('/transaction_detail/loadform') }}/${$('.total_price').text()}/${money_received}`)
                .done(response => {
                    $('#totalrp').val('Rp. '+ response.totalrp);
                    $('#final_pricerp').val('Rp. '+ response.final_pricerp);
                    $('#final_price').val(response.final_price);
                    $('.show-final_price').text('Bayar: Rp. '+ response.final_pricerp);
                    $('.show-in_word').text(response.in_word);

                    $('#changerp').val('Rp.'+ response.changerp);
                    if ($('#money_received').val() != 0) {
                        $('.show-final_price').text('Change: Rp. '+ response.changerp);
                        $('.show-in_word').text(response.change_in_word);
                    }
                })
                .fail(errors => {
                    alert('Tidak dapat menampilkan data');
                    return;
                })
        }
    </script>
@endpush

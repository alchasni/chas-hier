@extends('layouts.master')

@section('title')
    Product
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Product</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    @if (auth()->user()->level == 1)
                        <button onclick="createOne('{{ route('product.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i></button>
                        <button onclick="printBarcode('{{ route('product.print_barcode') }}')" class="btn btn-info btn-sm btn-flat">
                            <i class="fa fa-barcode"></i>
                        </button>
                    @endif
                </div>
                <div class="box-body table-responsive">
                    <form action="" method="post" class="form-product">
                        @csrf
                        <table class="table table-stiped table-bordered">
                            <thead>
                            <th width="5%">
                                <input type="checkbox" name="select_all" id="select_all">
                            </th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Buy price</th>
                            <th>Sell price</th>
                            <th>Discount</th>
                            <th>Stock</th>
                            <th><i class="fa fa-cog"></i></th>
                            </thead>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @includeIf('product.form')
@endsection

@push('scripts')
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('product.data') }}',
                },
                columns: [
                    {data: 'select_all', searchable: false, sortable: false},
                    {data: 'name'},
                    {data: 'category_name'},
                    {data: 'buy_price', searchable: false},
                    {data: 'sell_price', searchable: false},
                    {data: 'discount', searchable: false},
                    {data: 'stock', searchable: false},
                    {data: 'action', searchable: false, sortable: false},
                ]
            });

            $('#modal-form').validator().on('submit', function (e) {
                if (!e.preventDefault()) {
                    $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                        .done((response) => {
                            $('#modal-form').modal('hide');
                            table.ajax.reload();
                        })
                        .fail((errors) => {
                            alert(errors.responseText);
                            return;
                        });
                }
            });

            $('[name=select_all]').on('click', function () {
                $(':checkbox').prop('checked', this.checked);
            });
        });

        function createOne(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Create Product');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=name]').focus();
        }

        function updateOne(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Product');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=name]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                    $('#modal-form [name=category_id]').val(response.category_id);
                    $('#modal-form [name=buy_price]').val(response.buy_price);
                    $('#modal-form [name=sell_price]').val(response.sell_price);
                    $('#modal-form [name=discount]').val(response.discount);
                    $('#modal-form [name=stock]').val(response.stock);
                })
                .fail((errors) => {
                    alert('Failed to show data');
                    return;
                });
        }

        function deleteOne(url, name) {
            if (confirm(`Are you sure you want to delete "${name}"?`)) {
                $.post(url, {
                    '_token': $('[name=csrf-token]').attr('content'),
                    '_method': 'delete'
                })
                    .done((response) => {
                        table.ajax.reload();
                    })
                    .fail((errors) => {
                        alert('Failed to delete data');
                        return;
                    });
            }
        }

        function updateStock(url, name) {
            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');

            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Product');

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name).prop('disabled', true);
                    $('#modal-form [name=category_id]').val(response.category_id);
                    $('#modal-form [name=buy_price]').val(response.buy_price);
                    $('#modal-form [name=sell_price]').val(response.sell_price);
                    $('#modal-form [name=discount]').val(response.discount);

                    $('#modal-form [name=category_id]').closest('.form-group').hide();
                    $('#modal-form [name=buy_price]').closest('.form-group').hide();
                    $('#modal-form [name=sell_price]').closest('.form-group').hide();
                    $('#modal-form [name=discount]').closest('.form-group').hide();

                    $('#modal-form [name=stock]').val(response.stock);
                })
                .fail((errors) => {
                    alert('Failed to show data');
                });
        }

        function printBarcode(url) {
            if ($('input:checked').length < 1) {
                alert('Select the product to print');
            } else if ($('input:checked').length < 3) {
                alert('Select at least 3 to print');
            } else {
                $('.form-product')
                    .attr('target', '_blank')
                    .attr('action', url)
                    .submit();
            }
        }
    </script>
@endpush

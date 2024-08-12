@extends('layouts.master')

@section('title')
    Product Category
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Category</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <button onclick="create('{{ route('category.store') }}')" class="btn btn-success btn-sm btn-flat"><i class="fa fa-plus-circle"></i></button>
                </div>
                <div class="box-body table-responsive">
                    <table class="table table-stiped table-bordered">
                        <thead>
                        <th>Product Category</th>
                        <th><i class="fa fa-cog"></i></th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @includeIf('category.form')
@endsection

@push('scripts')
    <script>
        let table;

        $(function () {
            table = $('.table').DataTable({
                processing: true,
                autoWidth: false,
                ajax: {
                    url: '{{ route('category.data') }}',
                },
                columns: [
                    {data: 'name'},
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
        });

        function create(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Create Category');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('post');
            $('#modal-form [name=category_name]').focus();
        }

        function editForm(url) {
            $('#modal-form').modal('show');
            $('#modal-form .modal-title').text('Edit Category');

            $('#modal-form form')[0].reset();
            $('#modal-form form').attr('action', url);
            $('#modal-form [name=_method]').val('put');
            $('#modal-form [name=category_name]').focus();

            $.get(url)
                .done((response) => {
                    $('#modal-form [name=name]').val(response.name);
                })
                .fail((errors) => {
                    alert('Failed to show data');
                    return;
                });
        }

        function deleteData(url, name) {
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
    </script>
@endpush

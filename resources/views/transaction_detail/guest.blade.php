<div class="modal fade" id="modal-guest" tabindex="-1" role="dialog" aria-labelledby="modal-guest">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pilih Member</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-guest">
                    <thead>
                        <th width="5%">No</th>
                        <th>Nama</th>
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th><i class="fa fa-cog"></i></th>
                    </thead>
                    <tbody>
                        @foreach ($guests as $key => $guest)
                            <tr>
                                <td width="5%">{{ $key+1 }}</td>
                                <td>{{ $guest->name }}</td>
                                <td>{{ $guest->phone_number }}</td>
                                <td>{{ $guest->address }}</td>
                                <td>
                                    <a href="#" class="btn btn-primary btn-xs btn-flat"
                                        onclick="chooseGuest('{{ $guest->guest_id }}', '{{ $guest->name }}')">
                                        <i class="fa fa-check-circle"></i>
                                        Pilih
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

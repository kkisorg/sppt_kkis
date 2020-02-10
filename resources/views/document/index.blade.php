@extends('layout.base')

@section('title', 'Daftar Dokumen')

@section('extra_css')
<style>
    th[name='manage-col'] {
        white-space: nowrap;
        width: 15%;
    }
    th[name='no-col'] {
        width: 10%;
    }
    th[name='type-col'] {
        width: 20%;
    }
    th[name='name-col'] {
        width: 30%;
    }
    th[name='publish-datetime-col'] {
        width: 25%;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div class="col xs-12 col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 col-lg-8 col-lg-offset-2">
            <div class="form-group">
                <a href="/document/create" class="btn btn-primary" role="button">Buat Dokumen Baru</a>
            </div>
            <table id="media-table" class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th name="no-col"> No.</th>
                        <th name="type-col" class="hidden-xs"> Tipe Dokumen </th>
                        <th name="name-col"> Name Dokumen </th>
                        <th name="publish-datetime-col" > Waktu Publikasi </th>
                        <th name="manage-col"> Kelola </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documents as $document)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="hidden-xs">{{ $document->type }}</td>
                        <td>{{ $document->name }}</td>
                        <td>{{ $document->publish_datetime }}</td>
                        <td>
                            <div class="list-group">
                                <a class="list-group-item list-group-item-warning" href="/document/edit/{{ $document->id }}"> Ubah </a>
                                <a class="list-group-item list-group-item-danger" href="/document/delete/{{ $document->id }}" onclick="return confirm('Apakah Anda yakin menghapus dokumen ini?\nPenghapusan ini tidak dapat dibatalkan.');"> Hapus </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layout.base')

@section('title', 'Kelola User')

@section('extra_css')
<style>
    th[name='no-col'] {
        width: 5%;
    }
    th[name='name-col'] {
        width: 25%;
    }
    th[name='organization-col'] {
        width: 25%;
    }
    th[name='active-col'] {
        white-space: nowrap;
        width: 15%;
    }
    th[name='block-col'] {
        white-space: nowrap;
        width: 15%;
    }
    th[name='admin-col'] {
        white-space: nowrap;
        width: 15%;
    }
</style>
@endsection

@section('content')
    @include('layout.message')
    <div class="row">
        <div>
            <h3><b> Kelola Akun </b></h3>
        </div>
        <table id="account-management-table" class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th name="no-col"> No.</th>
                    <th name="name-col"> Nama </th>
                    <th name="organization-col" class="hidden-xs"> Nama Ranting/Unit </th>
                    <th name="active-col"> User Aktif </th>
                    <th name="block-col"> User Diblokir </th>
                    <th name="admin-col"> Admin </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td class="hidden-xs" name="organization-col">{{ $user->organization_name }}</td>
                    <td>
                        @if ($user->is_active)
                        <div class="text-center">&#x2714;</div>
                        @else
                        <div class="text-center">&#x2718;</div>
                        <a class="btn btn-warning" href="/account_management/resend_activation_email/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin mengirimkan ulang email aktivasi user ini?');"> Kirim Ulang Email Aktivasi </a>
                        <a class="btn btn-danger" href="/account_management/force_activate/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin mengaktifkan user ini?');"> Paksa Aktifkan </a>
                        @endif
                    </td>
                    <td>
                        @if ($user->is_blocked)
                        <div class="text-center">&#x2714;</div>
                        @if ($current_user->id !== $user->id)
                        <a class="btn btn-danger" href="/account_management/update_block_status/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin membuka blokir akun user ini?');"> Buka Blokir </a>
                        @endif
                        @else
                        <div class="text-center">&#x2718;</div>
                        @if ($current_user->id !== $user->id)
                        <a class="btn btn-warning" href="/account_management/update_block_status/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin memblokir akun user ini?');"> Blokir </a>
                        @endif
                        @endif
                    </td>
                    <td>
                        @if ($user->is_admin)
                        <div class="text-center">&#x2714;</div>
                        @if ($current_user->id !== $user->id)
                        <a class="btn btn-danger" href="/account_management/update_admin_role/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin menurunkan user ini dari admin?');"> Turunkan dari Admin </a>
                        @endif
                        @else
                        <div class="text-center">&#x2718;</div>
                        @if ($current_user->id !== $user->id)
                        <a class="btn btn-warning" href="/account_management/update_admin_role/{{ $user->id }}" onclick="return confirm('Apakah Anda yakin menaikkan user ini sebagai admin?');"> Naikkan sebagai Admin </a>
                        @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

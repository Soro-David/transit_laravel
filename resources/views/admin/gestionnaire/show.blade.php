@extends('admin.layouts.admin')

@section('content-header',)

@section('content')
<section class="py-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                            <h2>Liste des utilisateurs</h2>
                            <div class="container">
                                <table id="users-table" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Rôle</th>
                                            <th>Date de création</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        $(document).ready(function () {
            $('#users-table').DataTable({                
                processing: true,
                serverSide: true,
                ajax: '{{ route('managers.getUsers') }}',
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'first_name', name: 'first_name' },
                    { data: 'email', name: 'email' },
                    { data: 'role', name: 'role' },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

        });
        </script>
      
@endsection

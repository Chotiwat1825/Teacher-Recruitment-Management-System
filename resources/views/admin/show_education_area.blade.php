@extends('adminlte::page')

@section('title', 'ข้อมูลเขตพื้นที่การศึกษา')

@section('content_header')
    <h1>ข้อมูลเขตพื้นที่การศึกษา</h1>
@stop
@vite(['resources/js/app.js'])
@extends('layouts.sweetalert2')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">ตารางข้อมูลเขตพื้นที่การศึกษา</h3>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered">
                <thead>
                    <tr>
                        <th scope="col" width="10%">ลำดับ</th>
                        <th scope="col" width="70%">ชื่อสถานศึกษา</th>
                        <th scope="col" width="20%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($education_area as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name_education }}</td>
                            <td>
                                <div class="d-flex justify-content-center">
                                    <form action="{{ route('admin.education.area.edit') }}" method="post">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                        <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i></button>
                                    </form>

                                    <a href="{{ route('admin.education.area.delete') }}" value="{{ $item->id }}"
                                        class="btn btn-danger" onclick="return confirm('คุณต้องการลบข้อมูลนี้หรือไม่?')"><i
                                            class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- {{ $geteducation_area->links() }} --}}
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#example1').DataTable();
        });
    </script>
@endsection

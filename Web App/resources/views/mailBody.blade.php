@extends('layouts.layout')
@section('content')
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">MailBox</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">MailBox</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
      
    <section class="content">
      <div class="row">
        <!-- /.col -->
        <div class="col-md-12"> 
          <div class="card card-primary card-outline px-5 py-3">
                 <?php 
                    $msg_rep = preg_replace("/.*<body[^>]*>|<\/body>.*/si", "", $msg);
                    echo $msg_rep 
                 ?>
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
@endsection
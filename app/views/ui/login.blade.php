@extends('layouts.master')

@section('content')

    <div class="col-lg-4 col-lg-offset-4 col-sm-4 col-sm-offset-4">
        <div class='panel panel-default'>
            <div class="panel-body">
                <div class="col-sm-12">
                    <form class="form-signin" role="form" action="" method="post">
                        <h3 class="form-signin-heading">Please sign in</h3>
                        <input type="text" name='username' class="form-control" placeholder="Username" required autofocus>
                        <input type="password" name='password' class="form-control" placeholder="Password" required>
                        <br/>

                        @if(!empty($message))
                            <div class="alert alert-danger error">
                                <i class="fa fa-2x fa-exclamation-circle"></i> <span class="text">{{ $message }}</span>
                            </div>
                        @endif
                        <p>
                            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
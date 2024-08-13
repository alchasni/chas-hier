@extends('layouts.auth')

@section('login')
    <div class="login-box">
        <div class="login-box-body">
            <form action="{{ route('login') }}" method="post" class="form-login">
                @csrf
                <div class="form-group has-feedback @error('email') has-error @enderror">
                    <input type="email" name="email" class="form-control" placeholder="Email" required value="{{ old('email') }}" autofocus>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                    @error('email')
                    <span class="help-block">{{ $message }}</span>
                    @else
                        <span class="help-block with-errors"></span>
                        @enderror
                </div>
                <div class="form-group has-feedback @error('password') has-error @enderror">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                    @error('password')
                    <span class="help-block">{{ $message }}</span>
                    @else
                        <span class="help-block with-errors"></span>
                        @enderror
                </div>
                <div class="form-group" style="margin-top: 50px;">
                    <div class="checkbox icheck">
                        <label>
                            <input type="checkbox"> &nbsp &nbsp Remember Me
                        </label>
                    </div>
                </div>
                <div class="form-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </form>
        </div>
    </div>

    @if (session('errors'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                alert("{{ session('errors')->first() }}");
            });
        </script>
    @endif
@endsection

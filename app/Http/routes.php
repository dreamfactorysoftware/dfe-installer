<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/',
    [
        'as' => 'home',
        function (){
            return view('index');
        },
    ]);

Route::post('/',
    function (Request $request){
        $_data = $request->input();
        array_forget($_data, '_token');
        $_env = null;

        if (!empty($_data)) {
            foreach ($_data as $_key => $_value) {
                $_env .= 'export FACTER_' . trim(str_replace('-', '_', strtoupper($_key))) . '=' . $_value . PHP_EOL;
            }

            if (file_put_contents(storage_path('.install.env'), $_env)) {
                Session::flash('success', 'Installation file written.');
            }
        }

        return Redirect::home();
    });

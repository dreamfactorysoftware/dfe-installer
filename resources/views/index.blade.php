@extends('layouts.main')
<?php
use DreamFactory\Enterprise\Common\Providers\InspectionServiceProvider;

$_required = config('dfe.required-packages',[]);
$_service = \App::make(InspectionServiceProvider::IOC_NAME);
?>
@section('content')
    <h2>DreamFactory Enterprise&trade; Installer</h2>
    <section id="inspection">
        <h3>Required Components</h3>
        <p>The following is a list of components that are required to run DreamFactory Enterprise&trade;. Those that are not
            installed will be noted below. These must be installed before installation begins.</p>
        <table style="width: 50%" class="table table-bordered table-condensed table-responsive"><tr><th>Package</th><th>Status</th></tr>
            <?php
            foreach ($_required as $_package) {
                $_has = $_service->hasPackage($_package);
                $_status = $_has ? 'text-success' : 'text-danger';
                echo '<tr><td class="'.$_status.'">' .
                        $_package .
                        '</td><td class="'.$_status.'">' .
                        ($_service->hasPackage($_package) ? 'Yes' : 'No') .
                        '</td></tr>';
            }
            ?>
        </table>
    </section>

    <section id="options">
        <h3>Installation Settings</h3>

        <div class="row"></div>
    </section>

    <section id="confirmation">
        <h3>Confirmation</h3>

        <div class="row"></div>
    </section>
@stop

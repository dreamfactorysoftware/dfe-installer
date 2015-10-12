<?php
/**
 * @type array $requirements
 */
?>
@extends('layouts.main')
@section('content')
    <section id="section-inspection">
        <h2>Configuration Created!</h2>

        <p>The information supplied on the previous screen has been written to a file called <span class="text-muted">.env-install</span>
            in the root of where you installed this software. This file is utilized by the <span class="text-muted">install.sh</span>
            script. That is actually the next step in the installation.</p>

        <p>Go back to where you started up the PHP configuration utility and press Control-C to break out of the server.
            At this point, simply issue the following command:</p>

        <pre>
            ubuntu@console:~/dfe-installer$ sudo ./install.sh
        </pre>

        <p>If you experience any issues, you may try again or contact DreamFactory customer support at <a
                    href="mailto:support@dreamfactory.com">support@dreamfactory.com</a></p>

        <div class="form-actions">
            <button type="submit" class="btn btn-md btn-success">
                <i class="fa fa-arrow-circle-right"></i> Save Configuration
            </button>
        </div>
    </section>
@stop

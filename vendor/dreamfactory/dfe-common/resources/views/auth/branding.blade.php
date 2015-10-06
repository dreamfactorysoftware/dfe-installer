@section('auth.branding')
	<h3><img src="/vendor/dfe-common/img/logo-dfe.png" alt="" />
		<small>{{ $pageDisplayName or config('dfe.common.display-name') }}
			<span>{{ config('dfe.common.display-version') }}</span>
		</small>
	</h3>
@overwrite

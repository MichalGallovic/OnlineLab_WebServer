<link href="{!! trans('ROOT_PATH ') !!}includes/modules/profile/css/default.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{!! trans('ROOT_PATH ') !!}includes/modules/profile/js/default.js"></script>

<div id="profile">
	
	<div id="profile-warning" class="warning_msg"></div>
	<div id="profile-succes" class="succes_msg"></div>
	
	<form action="" id="update-prfile-form" method="post" enctype="multipart/form-data" onSubmit="set_profile();return false;">
	<table>
		<tr>
			<td class="first">{!! trans('USR_LOGIN ') !!}</td>
			<td><input type="text" value="{!! trans('USER_LOGIN ') !!}" name="login" readonly="readonly" class="readonly"/><span class="info">{!! trans('USR_I ') !!}</span></td>
		</tr>
		<tr>
			<td>{!! trans('USR_ROLE ') !!}</td>
			<td>                            
                            <select name="role_code">
                                {!! trans('RULE_ACCESS ') !!}
                            </select>
                        </td>
		</tr>
		<tr>
			<td>{!! trans('USR_NAME ') !!}</td>
			<td><input type="text" value="{!! trans('USER_NAME ') !!}" name="name" /></td>
		</tr>
		<tr>
			<td>{!! trans('USR_SURNAME ') !!}</td>
			<td><input type="text" value="{!! trans('USER_SURNAME ') !!}" name="surname" /></td>
		</tr>
		<tr>
			<td>E-mail</td>
			<td><input type="text" value="{!! trans('USER_EMAIL ') !!}" name="email" /></td>
		</tr>
		<tr>
			<td>{!! trans('USR_LANG ') !!}</td>
			<td>
				<select name="language_code">
				<!-- BEGIN DYNAMIC BLOCK: language_row -->
				<option {!! trans('Profile::LANG_SELECTED ') !!} value="{!! trans('LANG_CODE ') !!}">{!! trans('LANG_NAME ') !!}</option>
				<!-- END DYNAMIC BLOCK: language_row -->
				</select>
			</td>
		</tr>
		<tr>
			<td>{!! trans('USR_NEW_PASSWORD ') !!}</td>
			<td><input type="password" value="" name="pass" /><span class="info">{!! trans('USR_NEW_PASSWORD_I ') !!}</span></td>
		</tr>
		<tr>
			<td>{!! trans('USR_NEW_PASSWORD_R ') !!}</td>
			<td><input type="password" value="" name="pass2" /></td>
		</tr>
		<tr style="height:20px;">
			<td colspan="2"></td>
		</tr>
		<tr>
			<td style="text-align:left;">
				<input type="submit" value="{!! trans('USR_SAVE ') !!}" class="default-submit-btn" />
				<input type="hidden" value="1" name="attempt_profile_update">
			</td>
			<td>
				<div id="profile-process-loader">
					<img width="25" alt="ajax-loader" src="{!! trans('ROOT_PATH ') !!}includes/modules/profile/images/3.gif">
				</div>
			</td>
		</tr>
	</table>
	</form>
</div>

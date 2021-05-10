<div class="title">{t}Flag user{/t}</div>
<div class="content">

	<h1>Is <em>{$user->username|escape}</em> an abusive user?</h1>

	<p>
		If you think this user violates
		<a href="{$HTTP_SCHEMA}://{$URL_HOST}/legal:terms-of-service" target="_blank">Terms of Service</a>
		of {$SERVICE_NAME},
		posts objectionable content, may offend by their actions etc., you can
		flag them as abusive.
	</p>
	<p>
		No user will be automatically blocked, banned nor removed but the responsible authorities will
		be notified about the user and (if necessary) take some action.
	</p>
	<p>
		Click below to toggle the flag.
	</p>

	<p id="flag-user-options-flag" style="text-align: center; {if $flagged}display: none;{/if} ">
		<span style="color: #4B4; font-size: 150%; border: 1px solid #000; padding: 3px; margin: 5px;">{t}this user is OK{/t}</span>
		<a style="color: #CCC; font-size: 150%; border: 1px solid #DDD; padding: 3px; margin: 5px; text-decoration: none;" href="javascript:;"
		onclick="Wikijump.modules.FlagUserModule.listeners.setFlag(event, {$user->id}, true)">{t}this user is abusive{/t}</a>
	</p>
	<p id="flag-user-options-unflag" style="text-align: center;{if !$flagged}display: none;{/if}">
		<a style="color: #CCC; font-size: 150%; border: 1px solid #DDD; padding: 3px; margin: 5px; text-decoration: none;" href="javascript:;"
		onclick="Wikijump.modules.FlagUserModule.listeners.setFlag(event,{$user->id}, false)">{t}this user is OK{/t}</a>
		<span style="color: #B44;font-size: 150%; border: 1px solid #000; padding: 3px; margin: 5px;">{t}this user is abusive{/t}</span>
	</p>

</div>
<div class="button-bar">
	<a href="javascript:;" onclick="OZONE.dialog.cleanAll()">{t}close this window{/t}</a>
</div>

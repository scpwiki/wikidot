<h1>{t}Account settings{/t}</h1>


<div>
	<h3><a href="javascript:;" onclick="OZONE.ajax.requestModule('Account/Settings/ASEmailModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}My email address{/t}</a></h3>
	<p>
		{t}Your primary email address is now{/t}: {$user->email|escape}.
	</p>
</div>

<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASPasswordModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Change password{/t}</a></h3>
	<p>
		{t}Simply change your access password if you need or want.{/t}
	</p>
</div>



<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASNotificationsModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Notifications - online &amp; private RSS &amp; email{/t}</a></h3>
	<p>
		{t}Configure the way Wikijump informs you about events related to your presence here.{/t}
	</p>
</div>

{*
<div>
	<h3><a href="javascript:;"  onclick="Wikijump.modules.AccountModule.utils.loadModule('am-wiki-newsletters')">{t}Wiki Newsletters{/t}</a></h3>
	<p>
		{t}Tell us if you want to receive email newsletters from the Wikis you are a member of.{/t}
	</p>
</div>
*}
<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASMessagesModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Receiving private messages{/t}</a></h3>
	<p>
		{t}Is everybody allowed to send you a private message? Change this setting if you wish.{/t}
	</p>
</div>

<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASInvitationsModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Receiving invitations{/t}</a></h3>
	<p>
		{t}Do you want to receive invitations to participate in other sites?{/t}
	</p>
</div>

<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASBlockedModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Blocked users{/t}</a></h3>
	<p>
		{t}Configure the list of users you do not want to hear or receive anything from.{/t}
	</p>
</div>

<div>
	<h3><a href="javascript:;"  onclick="OZONE.ajax.requestModule('Account/Settings/ASLanguageModule', null, Wikijump.modules.AccountModule.callbacks.menuClick)">{t}Preferred language{/t}</a></h3>
	<p>
		{t}Choose the language you would prefer to use.{/t}
	</p>
</div>

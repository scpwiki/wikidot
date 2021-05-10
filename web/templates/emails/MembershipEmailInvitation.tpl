Hello {$name},

{if $message2}
{$message2|wordwrap}

The original message is included below:

-----------------------------------------

{/if}
{$user->username} {if $user->real_name}({$user->real_name}){/if} would like to invite you
to join members of the wiki website "{$site->getName()}"
created at {$SERVICE_NAME} and located at the address
{$HTTP_SCHEMA}://{$site->getDomain()|escape}.
{if $message!=""}

{$message|wordwrap}{/if}

Signing up is easy and takes less than a minute. If you already have
an account at {$SERVICE_NAME},
you will be able to join the mentioned
Site.

To proceed or learn more click the follow link:
{$HTTP_SCHEMA}://{$URL_HOST}/invitation/hash/{$hash}

See you

{$user->username|escape} {if $user->real_name}({$user->real_name}){/if}


P.S. If you do not want to accept this invitation - just ignore it.
If you believe this invitation is an abuse - please report it to:

{$SUPPORT_EMAIL}

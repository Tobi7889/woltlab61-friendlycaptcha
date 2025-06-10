{* 
  © 2024–2025 SpeedIT Solutions UG (haftungsbeschränkt), Isernhagen, Germany
  Licensed use only. Unauthorized distribution or modification is not permitted.
  License: https://www.speedit.org/
*}

<section class="section">
    <h2 class="sectionTitle">{lang}wcf.friendlycaptcha.title{/lang}</h2>
    <dl class="{if $errorField|isset && $errorField == 'friendlycaptchaString'}formError{/if}">
        <dt></dt>
        <dd>
            {if FRIENDLYCAPTCHA_VERSION == 'v1'}
                {if FRIENDLYCAPTCHA_ENDPOINT == 'eu'}
                    {assign var='puzzleEndpoint' value='https://eu.friendlycaptcha.com/api/v1/puzzle'}
                {else}
                    {assign var='puzzleEndpoint' value='https://api.friendlycaptcha.com/api/v1/puzzle'}
                {/if}
                <div class="frc-captcha" data-sitekey="{FRIENDLYCAPTCHA_SITEKEY|encodeJS}" data-theme="{FRIENDLYCAPTCHA_THEME}" data-puzzle-endpoint="{$puzzleEndpoint}" data-lang="{FRIENDLYCAPTCHA_LANGUAGE}"></div>
            {else}
                <div class="frc-captcha" data-sitekey="{FRIENDLYCAPTCHA_SITEKEY|encodeJS}" data-theme="{FRIENDLYCAPTCHA_THEME}" data-api-endpoint="{FRIENDLYCAPTCHA_ENDPOINT}" lang="{FRIENDLYCAPTCHA_LANGUAGE}"></div>
            {/if}
            {if (($errorType|isset && $errorType|is_array && $errorType[friendlycaptchaString]|isset) || ($errorField|isset && $errorField == 'friendlycaptchaString'))}
                {if $errorType|is_array && $errorType[friendlycaptchaString]|isset}
                    {assign var='__errorType' value=$errorType[friendlycaptchaString]}
                {else}
                    {assign var='__errorType' value=$errorType}
                {/if}
                <small class="innerError">
                    {if $__errorType == 'empty'}
                        {lang}wcf.global.form.error.empty{/lang}
                    {else}
                        {lang}wcf.friendlycaptcha.error.friendlycaptchaString.{$__errorType}{/lang}
                    {/if}
                </small>
            {/if}
        </dd>
    </dl>
    <script data-relocate="true">
        if (!window.FriendlyCaptcha) {
            var script = document.createElement('script');
            script.async = true;
            script.defer = true;
            var version = '{FRIENDLYCAPTCHA_VERSION}';
            if (version === 'v1') {
                script.type = 'module';
                script.src = 'https://cdn.jsdelivr.net/npm/friendly-challenge@latest/widget.module.min.js';
                document.head.appendChild(script);
            } else {
                script.type = 'module';
                script.src = 'https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@latest/site.min.js';
                document.head.appendChild(script);
                var compat = document.createElement('script');
                compat.async = true;
                compat.defer = true;
                compat.nomodule = true;
                compat.src = 'https://cdn.jsdelivr.net/npm/@friendlycaptcha/sdk@latest/site.compat.min.js';
                document.head.appendChild(compat);
            }
        }
    </script>
</section>
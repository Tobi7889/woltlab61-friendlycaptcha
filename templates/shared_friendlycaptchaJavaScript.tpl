{* 
  © 2024–2025 SpeedIT Solutions UG (haftungsbeschränkt), Isernhagen, Germany
  Licensed use only. Unauthorized distribution or modification is not permitted.
  License: https://www.speedit.org/
*}

{if CAPTCHA_TYPE == 'software.speedit.woltlab.suite.core.security.antispam.friendlycaptcha' && FRIENDLYCAPTCHA_SITEKEY && FRIENDLYCAPTCHA_APIKEY}
        <script data-relocate="true">
                require(['SpeeditSoftware/FriendlyCaptcha/FriendlyCaptchaWidget'], function(Widget) {
                        Widget.setup();
                });
        </script>
{/if}

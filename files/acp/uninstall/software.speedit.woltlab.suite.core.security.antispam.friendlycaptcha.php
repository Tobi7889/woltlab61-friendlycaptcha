<?php

/**
 * © 2024–2025 SpeedIT Solutions UG (haftungsbeschränkt), Isernhagen, Germany
 * 
 * This file is part of a licensed product. Redistribution, modification, or reuse
 * outside the scope of the applicable license agreement is strictly prohibited.
 * 
 * For more information, please refer to the full license terms:
 * https://www.speedit.org/
 */

/**
 * Handles the uninstallation of the FriendlyCaptcha integration.
 */

use wcf\system\WCF;

$sql = "UPDATE  wcf1_option
        SET     optionValue = ?
        WHERE   optionName = ?
            AND optionValue = ?";
$statement = WCF::getDB()->prepare($sql);
$statement->execute([
    '',
    'captcha_type',
    'software.speedit.woltlab.suite.core.security.antispam.friendlycaptcha',
]);

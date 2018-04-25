<?php

$emailTemplates = [
    "password_reminder" => [
            "path" => "remind_pass_email.html",
            "subject" => "You password reminder",
            "body" => "<p>You password reminder</p>",
            "alt" => "Your alternative text"
        ],
    "password_changed" => [
            "path" => "password_changed.html",
            "subject" => "You password has been successfully changed",
            "body" => "<p>We are letting you know that you password was successfully changed</p>",
            "alt" => "We are letting you know that you password was successfully changed"
        ],
    "invite" => [
            "path" => "invite.html",
            "subject" => "You have been invited to BreatheCo.de",
            "body" => "<p>You have been invited to BreatheCo.de</p>",
            "alt" => "You have been invited to BreatheCo.de"
        ]
];
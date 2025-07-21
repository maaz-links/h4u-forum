<?php

return [
    'attachments'=> [
        'limit' => 10,
    ],

    'email' => [
        'support_address' => 'support@hostessforyou.com'
    ],

    'reviews' => [
        'review_delay' => 3,
        'minimum_rating' => 3,
        'minimum_reviews_for_visibility' => 3,
    ],

    'chatcost' => [
        'standard' => 10,
        'verified' => 20,
        'topprofile' => 30,
        'verified_topprofile' => 40,
    ],


    //emailsubject and emailmessage should have same keys.
    'emailsubject' => [
        'warning' => "Account Warning: Please Review Your Recent Activity",
        'permaban' =>   'Account Permanently Banned - Final Notification',
        'tempban' =>  'Temporary Account Suspension Notice',
        'unban' =>  'Your Account Has Been Reinstated',
        'otp' =>  'Your OTP for login',
        'unlockchat' =>  'Someone just unlocked chat with you!',
        'freemessage'=> 'You have received a new message!',
        // 'review_remainder'=> 'Reminder to Leave a Review',
        'review_recieved'=> 'Someone left a Review',
    ],

    'emailmessage' => [
    'warning' => "Dear {username}, We have noticed activity on your account that violates our community guidelines. This is a formal warning to remind you of the importance of following our rules to maintain a safe and respectful environment for everyone. Continued violations may result in temporary or permanent suspension of your account. Thank you for your attention.",

    'permaban' => "Dear {username}, After a thorough review of your account and multiple prior warnings, we regret to inform you that your account has been permanently banned due to serious or repeated violations of our community guidelines. This decision is final and irreversible. We appreciate your past participation and wish you well moving forward.",

    'tempban' => "Dear {username}, Due to repeated violations of our community guidelines, your account has been temporarily suspended. - Suspended Until: {suspension_time} During this period, you will not be able to access your account. Your account will be automatically restored after the suspension period unless further violations occur. Thank you for your understanding.",

    'unban' => "Dear {username}, We are writing to inform you that your account has been reinstated and is now fully active. This decision was made after a review of your case, and we trust that you will continue to follow our Community Guidelines moving forward. Welcome back, and thank you for being part of our community.",

    'otp' => "Your OTP for login is {otp}. Do not share this with anyone.",

    'unlockchat' => "Dear {username}, Great news! {other_username} is interested in chatting and has just unlocked the chat with you. Open the app now to see who it is and start the conversation.",
    'freemessage' => "Dear {username}, {other_username} has sent you greeting message and wants to talk to you. Unlock Chat with her to continue conversation.",
    // 'review_remainder'=> 'Dear {username}, You have chatted with {other_username} for {delay} days. You can now leave your review for {other_username}.',
    'review_recieved'=> 'Dear {username}, {other_username} has left {rating} star rating for you. Leave your review for {other_username}.',
    ],
];
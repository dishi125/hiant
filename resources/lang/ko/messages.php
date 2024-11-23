<?php

return [

    /* Admin messages */
    "insert-success" => " has been created successfully.",
    "insert-error" => " has not been created successfully.",
    "update-success" => " has been updated successfully.",
    "update-error" => " has not been updated successfully.",
    "delete-success" => " has been deleted successfully.",
    "delete-error" => " has not been deleted successfully.",


    /* Api messages */
    "400" => "Ooops...Internal server error.",
    "200" => "success",
    "409" => "Please try again. This username is already exist.",
    "404" => "User doesn't exist.",
    "402" => "Your account has been deactivated. Please contact administrator.",
    "401" => "Credentials are invalid",
    "report-success" => "Thanks for letting us know, we will get back to you about this content soon. Check your email inbox for an update within the next few days.",
    "201" => "Sorry!!! Requested stream is closed",
    "3078" => "Sorry!!! You have not enough peals to convert diamonds.",
    "301" => "Sorry!!! You have not enough diamonds to send gift.",
    "203" => "Already Pk applied by another user.",
    "429" => "The number of users limit has been exceeded, but more requests could be performed upon a payment.",

    'authenticate' => [
        'logout' => 'You have been successfully logged out.'
    ],
    'user' => [
        'auth_token_not_found' => 'Authorization token not found',
        'token_invalid' => 'Invalid token',
        'token_expired' => 'Token expired',
        'store' => 'User registered successfully.',
        'success' => 'Successfully get user details.',
        'not-found' => 'User not found'
    ],

    'profile' => [
        'update' => 'User updated successfully.',
        'not-found' => 'User not found'
    ],

    'authenticate' => [
        'validate' => 'Email and password are required',
        'cred_invalid' => 'Credentials are invalid',
        'exception' => 'Could not create token',
        'success' => 'User login Successfully',
        'logout' => 'User logout Successfully',
        'update-token' => 'Token Updated successfully',
        'invalid-token' => 'In valid Token'
    ],
    'forgot-password' => [
        'email-not-exist' => "Email ID doesn't exists",
        'mobile-not-exist' => "Phone number doesn't exists",
        'success' => 'Password change successfully',
    ],
    'forgot-email' => [
        'mobile-not-exist' => "Phone number doesn't exists",
        'success' => 'Login Id send in Email successfully',
    ],
    'change-password' => [
        'success' => 'Password changed successfully',
        'password_not_match' => 'Old password does not correct.',
    ],
    'general' => [
        'laravel_error' => 'Please contact support team.',
        'success' => 'Succesfully Get list',
        'unauthenticated' => 'Not authenticated.',
    ],
    'register' => [
        'success' => 'You are successfully registered',
        'email_exist' => 'Email is already exists'
    ],
    'shop' => [
        'edit-success' => 'Shop get Successfully.',
        'delete-image-success' => 'Image deleted Successfully.',
        'update-success' => 'Shop update Successfully.',
        'instagram-share-success' => 'Image shared on instagram Successfully.',
        'empty' => 'Shop Not found.',
        'success' => 'Successfully get shop list',
        'max-shops' => 'You can create maximum :count shops only.',
        'portfolio-max-post' => 'You can upload maximum :count posts only.',
        'portfolio-per-day-max-post' => 'You can upload maximum :count posts per day.',
        'same-category-shop' => 'Same category shop already exists.',
        'portfolio-success' => 'Shop Portfolio added successfully',
        'inactive-success' => 'Please fill profile and portfolio at least 3 post',
        'post-get-success' => 'Successfully get shop post detail',
        'post-delete-success' => 'Successfully deleted shop post',
        'post-empty' => 'Shop post not found.',
        'follow-success' => 'Shop follow successfully.',
        'unfollow-success' => 'Shop unfollow successfully.',
        'status-success' => 'Successfully get shop status list.',
        'status-change-success' => 'Successfully changed shop status.',
    ],
    'shop-price' => [
        'list-success' => 'Shop price list successfully',
        'add-success' => 'Shop price added successfully',
        'edit-success' => 'Shop price get successfully',
        'update-success' => 'Shop price updated successfully',
        'delete-success' => 'Shop price deleted successfully',
        'empty' => 'Shop price Not found.'
    ],
    'shop-price-category' => [
        'list-success' => 'Shop price category list successfully',
        'add-success' => 'Shop price category added successfully',
        'edit-success' => 'Shop price category get successfully',
        'update-success' => 'Shop price category updated successfully',
        'delete-success' => 'Shop price category deleted successfully',
        'empty' => 'Shop Price category not found.'
    ],
    'user-profile' => [
        'success' => 'Succesfully get user profile',
        'your-post-success' => 'Succesfully get user posts',
        'search-history-success' => 'Succesfully get search history',
        'save-history-success' => 'Succesfully get save history',
        'plan-success' => 'Succesfully get credit plans',
        'plan-update-success' => 'Credit plan updated successfully',
        'profile-deactivate-success' => 'Profile deactivated successfully',
        'profile-activate-success' => 'Profile activated successfully',
        'update-success' => 'User profile updated successfully',
        'update-password-success' => 'Password updated successfully',
        'add-history-success' => 'Saved to profile history successfully',
        'remove-history-success' => 'Removed from profile history successfully',
        'old-password-error' => 'Old Password Incorrect',
        'sns-request-success' => 'SNS Reward requested Succesfully',
    ],
    'doctor' => [
        'edit-success' => 'Doctor get Successfully.',
        'update-success' => 'Doctor update Successfully.',
        'empty' => 'Doctor Not found.',
        'success' => 'Successfully get doctor list',
        'delete-success' => 'Successfully deleted doctor',
        'add-success' => 'Successfully added doctor',
        'list-success' => 'Successfully get doctors list',
    ],
    'hospital' => [
        'get-language-success' => 'Post languages get Successfully.',
        'edit-success' => 'Hospital get Successfully.',
        'update-success' => 'Hospital update Successfully.',
        'empty' => 'Hospital Not found.',
        'success' => 'Successfully get hospital list',
        'post-success' => 'Successfully get hospital posts',
        'post-add-success' => 'Successfully added hospital post',
        'post-delete-success' => 'Successfully deleted hospital',
        'status-success' => 'Successfully get hospital status list.',
        'status-change-success' => 'Successfully changed hospital status.',
    ],
    'category' => [
        'empty' => 'Category not found.',
        'success' => 'Category get successfully.',
        'currency-success' => 'Currency get successfully.',
    ],
    'post' => [
        'success' => 'Post get successfully.',
        'empty' => 'Post Not found.',
    ],
    'request-service' => [
        'add-success' => 'Booked service successfully.',
        'empty' => 'Customer not found.',
        'complete-success' => 'Service request set to completed successfully.',
        'noshow-success' => 'Service request set to no show successfully.',
        'cancel-success' => 'Service request set to cancel successfully.',
        'dismiss-success' => 'Service request dismissed successfully.',
        'dismiss-success' => 'Service request dismissed successfully.',
        'change-date-success' => 'Booking date changed successfully.',
        'memo-success' => 'Customer memo set successfully.',
        'get-revenue' => 'Get revenue successfully.',
        'credit-deduct' => 'Credit deducted successfully.',
    ],
    'review' => [
        'add-success' => 'Review added successfully.',
        'get-success' => 'Get review successfully.',
        'like-success' => 'Liked review successfully.',
        'delete-success' => 'Deleted review successfully.',
        'comment-like-success' => 'Liked review comment successfully.',
        'comment-edit-success' => 'Updated comment successfully.',
        'comment-delete-success' => 'Deleted comment successfully.',
        'comment-success' => 'Review comment added successfully.',
        'empty' => 'Review not found.',
        'comment-empty' => 'Review comment not found.',
    ],
    'community' => [
        'add-success' => 'Community added successfully.',
        'category-success' => 'Community category get successfully.',
        'get-success' => 'Get community successfully.',
        'like-success' => 'Liked community successfully.',
        'comment-like-success' => 'Liked community comment successfully.',
        'comment-success' => 'Community comment added successfully.',
        'empty' => 'Community not found.',
        'comment-empty' => 'Community comment not found.',
        'comment-delete-success' => 'Deleted comment successfully.',
        'comment-edit-success' => 'Updated comment successfully.',
    ],
    'report' => [
        'add-success' => 'Reported successfully.',
        'category-success' => 'Report category get successfully.',
    ],
    'home' => [
        'success' => 'Data get successfully.',
    ],
    'form-request' => [
        'success' => 'Request Successfully Send to Manager.',
        'already-exists' => 'You have already requested.',
    ],
    'messages' => [
        'success' => 'Successfully get chat list',
        'initiate-success' => 'Successfully initiate chat',
        'delete-success' => 'Successfully deleted chat',
    ],

];

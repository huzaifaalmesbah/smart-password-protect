<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SPP_Protection {

    public function init() {
        add_action('template_redirect', array($this, 'check_protection'));
        add_action('init', array($this, 'start_session'));
    }

    public function start_session() {
        if (!session_id()) {
            session_start();
        }
    }

    public function check_protection() {
        $options = get_option('spp_options');
    
        // Check if protection is enabled
        if (!isset($options['spp_enabled']) || $options['spp_enabled'] != 1) {
            return; // Protection is disabled, allow access
        }
    
        if (is_user_logged_in()) {
            return; // Allow access if the user is logged in
        }
    
        // Retrieve allowed IPs
        $allowed_ips = isset($options['spp_allowed_ips']) ? json_decode($options['spp_allowed_ips'], true) : [];
        
        // Ensure allowed IPs are trimmed and validated
        $allowed_ips = array_map('trim', $allowed_ips); // Trim each IP
        $allowed_ips = array_filter($allowed_ips, function($ip) {
            return filter_var($ip, FILTER_VALIDATE_IP); // Validate each IP
        });
    
        $password = isset($options['spp_password']) ? $options['spp_password'] : '';
    
        // Get the client's public IP address
        $client_ip = SPP_Helpers::get_public_ip();
    
        // Check if the current IP is in the allowed list
        if (in_array($client_ip, $allowed_ips)) {
            return; // Allow access if IP is allowed
        }
    
        // If session is set, allow access
        if (isset($_SESSION['spp_authenticated']) && $_SESSION['spp_authenticated']) {
            return;
        }
    
        // If password form is submitted and correct, set session
        if (isset($_POST['spp_password']) && $_POST['spp_password'] === $password) {
            $_SESSION['spp_authenticated'] = true;
            return;
        }
    
        // Show the password form
        $this->show_password_form();
        exit;
    }
    
    private function show_password_form() {
        ?>
        <style>
            .spp-password-form {
                max-width: 400px; /* Maximum width for the form */
                margin: 50px auto; /* Center the form */
                padding: 20px; /* Add some padding */
                border: 1px solid #ccc; /* Light gray border */
                border-radius: 5px; /* Rounded corners */
                background-color: #fff; /* White background */
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); /* Subtle shadow */
            }
    
            .spp-password-form h2 {
                margin-bottom: 20px; /* Space below the heading */
                font-size: 24px; /* Font size for the heading */
                color: #333; /* Darker color for text */
                text-align: center; /* Center align */
            }
    
            .spp-password-form p {
                margin-bottom: 15px; /* Space below the paragraph */
                color: #555; /* Medium gray text color */
                text-align: center; /* Center align */
            }
    
            .spp-password-form input[type="password"],
            .spp-password-form input[type="submit"] {
                width: 100%; /* Full width */
                padding: 10px; /* Padding inside inputs */
                margin-top: 10px; /* Space above each input */
                border: 1px solid #ccc; /* Light gray border */
                border-radius: 3px; /* Rounded corners */
                box-sizing: border-box; /* Include padding in width */
                font-size: 16px; /* Font size for inputs */
            }
    
            .spp-password-form input[type="password"]:focus,
            .spp-password-form input[type="submit"]:hover {
                border-color: #0073aa; /* Change border color on focus/hover */
                outline: none; /* Remove outline */
            }
    
            .spp-password-form input[type="submit"] {
                background-color: #0073aa; /* Button background color */
                color: white; /* White text */
                cursor: pointer; /* Pointer cursor on hover */
                transition: background-color 0.3s; /* Smooth transition */
            }
    
            .spp-password-form input[type="submit"]:hover {
                background-color: #005177; /* Darker button color on hover */
            }
        </style>
    
        <form method="post" class="spp-password-form">
            <h2>Password Required</h2>
            <p>Please enter the website password to access:</p>
            <input type="password" name="spp_password" required placeholder="Enter your password">
            <input type="submit" value="Login">
        </form>
        <?php
    }
    
    
}

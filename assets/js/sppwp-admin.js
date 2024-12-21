jQuery(document).ready(function($) {
    // Store allowed IPs in an array
    let allowed_ips = SPPWP_Data.allowed_ips || [];

    // Function to validate IP address
    function isValidIP(ip) {
        const ipRegex = /^(\d{1,3}\.){3}\d{1,3}$/;
        if (!ipRegex.test(ip)) return false;
        
        const parts = ip.split('.');
        return parts.every(part => {
            const num = parseInt(part, 10);
            return num >= 0 && num <= 255;
        });
    }

    // Function to render the IP list in the table
    function renderIPList() {
        let ipList = $('#allowed-ips-list');
        ipList.empty(); // Clear the table content before re-rendering

        // Loop through each IP in the array and add it to the table
        allowed_ips.forEach(function(ip, index) {
            ipList.append(`
                <tr data-ip="${ip}">
                    <td>${ip}</td>
                    <td>
                        <button type="button" 
                                class="remove-ip sppwp-button sppwp-button-danger" 
                                data-index="${index}">X</button>
                    </td>
                </tr>
            `);
        });

        // Update the hidden input field with the updated list of allowed IPs (as JSON)
        $('#sppwp_allowed_ips').val(JSON.stringify(allowed_ips));
    }

    // Event listener for adding a new IP
    $('.sppwp-ip-repeater').on('click', '#add-ip', function() {
        let newIp = $('#new-ip').val().trim();

        // Validate IP address
        if (!isValidIP(newIp)) {
            alert(SPPWP_Data.messages.invalid_ip);
            return;
        }

        // Check if input is not empty and IP is not already in the list
        if (newIp && !allowed_ips.includes(newIp)) {
            allowed_ips.push(newIp); // Add the new IP to the array
            renderIPList();          // Re-render the IP list
            $('#new-ip').val('');    // Clear the input field
        } else if (allowed_ips.includes(newIp)) {
            alert(SPPWP_Data.messages.ip_exists);
        }
    });

    // Enter key functionality for IP input
    $('#new-ip').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#add-ip').click();
        }
    });

    // Event delegation for removing an IP
    $('.sppwp-ip-table').on('click', '.remove-ip', function() {
        let index = $(this).data('index'); // Get the index of the IP to remove
        allowed_ips.splice(index, 1);      // Remove the IP from the array
        renderIPList();                    // Re-render the list
    });

    // Initially render the IP list when the page loads
    renderIPList();

    // Password and Enable Protection functionality
    var $passwordField = $('#sppwp_password');
    var $enabledCheckbox = $('#sppwp_enabled');
    var $switchLabel = $enabledCheckbox.closest('.sppwp-switch');

    // Function to update enable/disable state
    function updateEnableState() {
        if ($passwordField.val() === '') {
            $enabledCheckbox.prop('disabled', true);
            $switchLabel.addClass('sppwp-switch-disabled');
        } else {
            $enabledCheckbox.prop('disabled', false);
            $switchLabel.removeClass('sppwp-switch-disabled');
        }
    }

    // Initially check the enable state
    updateEnableState();

    // Enable/disable the checkbox when the user types in the password field
    $passwordField.on('input', function() {
        updateEnableState();
    });

    // Add loading state to form submission
    $('.sppwp-form').on('submit', function() {
        $(this).find('.sppwp-button-primary').addClass('sppwp-button-loading').prop('disabled', true);
    });

    // Initialize tooltips if they exist
    if ($.fn.tooltip) {
        $('.sppwp-tooltip').tooltip();
    }

    // Responsive table handling
    function checkTableResponsive() {
        const table = $('.sppwp-ip-table');
        if (table.width() > $('.sppwp-ip-repeater').width()) {
            table.addClass('sppwp-table-responsive');
        } else {
            table.removeClass('sppwp-table-responsive');
        }
    }

    // Check responsive state on load and resize
    $(window).on('load resize', checkTableResponsive);
});
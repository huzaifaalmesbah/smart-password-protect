jQuery(document).ready(function($) {

    // Store allowed IPs in an array
    let allowed_ips = SPP_Data.allowed_ips || [];

    // Function to render the IP list in the table
    function renderIPList() {
        let ipList = $('#allowed-ips-list');
        ipList.empty(); // Clear the table content before re-rendering

        // Loop through each IP in the array and add it to the table
        allowed_ips.forEach(function(ip, index) {
            ipList.append(`
                <tr>
                    <td>${ip}</td>
                    <td><button type="button" class="remove-ip" data-index="${index}">X</button></td>
                </tr>
            `);
        });

        // Update the hidden input field with the updated list of allowed IPs (as JSON)
        $('#spp_allowed_ips').val(JSON.stringify(allowed_ips));
    }

    // Event listener for adding a new IP
    $('#add-ip').on('click', function() {
        let newIp = $('#new-ip').val();

        // Check if input is not empty and IP is not already in the list
        if (newIp && !allowed_ips.includes(newIp)) {
            allowed_ips.push(newIp); // Add the new IP to the array
            renderIPList();          // Re-render the IP list
            $('#new-ip').val('');    // Clear the input field
        }
    });

    // Event delegation for removing an IP
    $(document).on('click', '.remove-ip', function() {
        let index = $(this).data('index'); // Get the index of the IP to remove
        allowed_ips.splice(index, 1);      // Remove the IP from the array
        renderIPList();                    // Re-render the list
    });

    // Initially render the IP list when the page loads
    renderIPList();

    // Tabs functionality
    function activateTab(tabId) {
        $('.nav-tab').removeClass('nav-tab-active');
        $('a[href="' + tabId + '"]').addClass('nav-tab-active');
        $('.tab-content').hide();
        $(tabId).show();
        localStorage.setItem('activeTab', tabId);
    }

    // Handle tab switching
    $('.nav-tab').on('click', function(event) {
        event.preventDefault();
        var selectedTab = $(this).attr('href');
        activateTab(selectedTab);
    });

    // Load the previously active tab from localStorage, or default to the first tab
    var storedTab = localStorage.getItem('activeTab');
    if (storedTab) {
        activateTab(storedTab);
    } else {
        activateTab('#general-settings');
    }

    // Password and Enable Protection functionality
    var $passwordField = $('#spp_password');
    var $enabledCheckbox = $('#spp_enabled');

    // Initially disable the checkbox if the password is empty.
    if ($passwordField.val() === '') {
        $enabledCheckbox.prop('disabled', true);
    }

    // Enable the checkbox when the user starts typing in the password field.
    $passwordField.on('input', function() {
        if ($(this).val() !== '') {
            $enabledCheckbox.prop('disabled', false);
        } else {
            $enabledCheckbox.prop('disabled', true);
        }
    });

});

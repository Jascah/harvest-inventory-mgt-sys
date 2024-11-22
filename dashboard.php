<?php
    // Include session and role check
    include 'rolecheck.php';
    include 'connection.php';

    session_start();
    if (!isset($_SESSION['name'])) {
        header("Location: sign_in.html");
        exit();
    }
    
    $user_name = $_SESSION['name'];
    $user_role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Harvest Inventory Management</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- JavaScript Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.1/chart.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

    
</head>
<body>
    <!-- Sidebar -->
    <div class="hamburger-menu"  onclick="toggleSidebar()">
        <div></div>
        <div></div>
        <div></div>
    </div>
    <span class="menu-label">Menu</span>
    <div class="sidebar">

        <h2>Welcome, <a href="#"><?php echo htmlspecialchars($user_name); ?></a></h2>
        <a href="#dashboard" class="nav-link" onclick="showContent('dashboard')">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="#storage" class="nav-link" onclick="showContent('storage')">
            <i class="fas fa-warehouse"></i> Storage
        </a>
        <a href="#inventory" class="nav-link" onclick="showContent('inventory')">
            <i class="fas fa-boxes"></i> Inventory
        </a>
        <a href="#dispatch-section"  class="nav-link" onclick = "showContent('dispatch-section')">
            <i class="fas fa-truck"></i> Dispatch
        </a>
        <a href="#settings" class="nav-link" onclick="showContent('settings')">
            <i class="fas fa-cogs"></i> Settings
        </a>
        <div class="logout">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
    </div>

    <!-- Main Content Sections -->
    <div class="main-content active" id="dashboard">
        <h1>Welcome</h1>
        <p>Welcome to the Harvest Inventory Management dashboard.</p>
        <div>
            <canvas id="inventorychart"></canvas>
        </div>
        <br>
        <div id="siloCapacitySection">
            <h2>Storage Capacity Left</h2>
            <table id="siloCapacityTable" class="styled-table">
                <thead>
                    <tr>
                        <th>Silo Location</th>
                        <th>Remaining Capacity (units)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be dynamically inserted here -->
                </tbody>
            </table>
        </div>
        <h2>Inventory Predictions</h2>
        <div>
            <canvas id="predictionChart" width="100" height="50"></canvas>
        </div>
    </div>

    <div class="main-content hidden" id="storage">
        <h1>Storage Management</h1>
        <!-- Storage Form -->
        <form id="storageForm">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="entry_id" id="entry_id">
            <input type="hidden" name="entered_by" id="entered_by" value="<?php echo $user_name; ?>">

            <label for="harvest_type">Harvest Type:</label>
            <input type="text" id="harvest_type" name="harvest_type" required>

            <label for="silo_location">Silo Location:</label>
            <input type="text" id="silo_location" name="silo_location" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <button type="submit" id="submitBtn">Save Entry</button>
        </form>

        <!-- Storage Data Display -->
        <div id="storageContent"></div>
    </div>

    <div class="main-content hidden" id="inventory">
        <h1>Inventory</h1>
        <div id="inventoryContent"></div>
        <div style="margin-top: 50px; text-align: center;">
            <a href="download.php" class="download-button">Download Inventory</a>
        </div>
    </div>

    <!-- Dispatch -->
    
    <div id="dispatch-section" class="main-content hidden">
        <h1>Dispatch Harvest Items</h1>
        <form id="dispatch-form" class="styled-form">
            <div class="form-group">
                <label for="harvest_type">Harvest Type:</label>
                <input type="text" id="harvest_type" name="harvest_type" placeholder="Enter harvest type" required>
            </div>

            <div class="form-group">
                <label for="silo_location">Silo Location:</label>
                <input type="text" id="silo_location" name="silo_location" placeholder="Enter silo location" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity to Dispatch:</label>
                <input type="number" id="quantity" name="quantity" min="1" placeholder="Enter quantity" required>
            </div>

            <div class="form-group">
                <label for="recipient">Recipient:</label>
                <input type="text" id="recipient" name="recipient" placeholder="Enter recipient name" required>
            </div>

            <button type="submit" class="btn-submit">Dispatch</button>
        </form>
        <div id="dispatch-message" class="message-box"></div>

        <div style ="margin-top: 20px;">
            <button id="generate-report-button" class="btn-submit">Dispatch Report</button>
            <br>
        </div>
        <div id="dispatch-table-container" style="margin-top: 20px;">
            <h2>Dispatch Records</h2><br>
            <div id="dispatchTable"></div>
        </div>

    </div>




    <div class="main-content hidden" id="settings">
        <form id="settingsForm">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter new name">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter new email">

            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter new password">

            <button type="submit">Update Settings</button>
        </form>
        <div id="settingsFeedback"></div>
    </div>

    <div class="footer">
        © 2024 Harvest Inventory Management. All rights reserved.
    </div>

    <!-- JavaScript -->
    <script>
       // Function to toggle the sidebar and hamburger menu
        function toggleSidebar() {
            // Select the hamburger menu and the sidebar
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const sidebar = document.querySelector('.sidebar');

            // Toggle the 'open' class on the hamburger menu
            hamburgerMenu.classList.toggle('open');

            // Toggle the 'open' class on the sidebar (if it exists)
            if (sidebar) {
                sidebar.classList.toggle('open');
            }

            // Optional: Add/remove overlay if the sidebar is present
            const overlay = document.querySelector('.overlay');
            if (sidebar && !overlay) {
                // Create overlay if not present
                const newOverlay = document.createElement('div');
                newOverlay.className = 'overlay';
                newOverlay.onclick = toggleSidebar; // Close sidebar when overlay is clicked
                document.body.appendChild(newOverlay);
            } else if (sidebar && overlay) {
                // Remove overlay when closing the sidebar
                overlay.remove();
            }
        }

        // Optional: Close the sidebar and reset menu on window resize
        window.addEventListener('resize', () => {
            const sidebar = document.querySelector('.sidebar');
            const hamburgerMenu = document.querySelector('.hamburger-menu');
            const overlay = document.querySelector('.overlay');

            // Close sidebar and reset hamburger menu if window is resized
            if (window.innerWidth > 768 && sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                hamburgerMenu.classList.remove('open');
                if (overlay) overlay.remove();
            }
        });


        // Show content section
        function showContent(sectionId) {
            // Hide all sections and show the selected one
            document.querySelectorAll('.main-content').forEach(section => {
                section.classList.add('hidden');
                section.classList.remove('active');
            });
            const targetSection = document.getElementById(sectionId);
            targetSection.classList.remove('hidden');
            targetSection.classList.add('active');

            // Load dynamic content if needed
            if (sectionId === 'storage') loadStorageContent();
            else if (sectionId === 'inventory') loadInventoryContent();
        }

        // Load Storage Content (READ)
        function loadStorageContent() {
            fetch('storage.php?action=view')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('storageContent').innerHTML = data;
                })
                .catch(error => console.error('Error loading storage content:', error));
        }

        // Handle Add/Update Entry (CREATE/UPDATE)
        document.getElementById('storageForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const entryData = Object.fromEntries(formData.entries());

            fetch('storage.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(entryData)
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.status === 'success') {
                    loadStorageContent();
                    this.reset();
                    document.getElementById('action').value = 'add';
                }
            })
            .catch(error => console.error('Error saving entry:', error));
        });

        // Edit Entry (UPDATE)
        function editEntry(entryId) {
            const harvestType = prompt('Enter new harvest type:');
            const siloLocation = prompt('Enter new silo location:');
            const quantity = prompt('Enter new quantity:');

            if (harvestType && siloLocation && quantity) {
                fetch('storage.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'update',
                        entry_id: entryId,
                        harvest_type: harvestType,
                        silo_location: siloLocation,
                        quantity: quantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') loadStorageContent();
                })
                .catch(error => console.error('Error editing entry:', error));
            }
        }

        // Delete Entry (DELETE)
        function loadStorageContent() {
            fetch('storage.php?action=view')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('storageContent').innerHTML = data;

                    const userRole = "<?php echo $_SESSION['role']; ?>"; // Fetch role from PHP session
                    const deleteButtons = document.querySelectorAll('.delete-button');
                    deleteButtons.forEach(button => {
                        if (userRole === 'admin' || userRole === 'manager') {
                            button.style.display = 'inline-block'; // Show delete buttons for admin and manager
                            // Add event listener to delete buttons
                            button.addEventListener('click', function() {
                                const entryId = button.getAttribute('data-entry-id'); // Ensure your button has a data-entry-id attribute
                                deleteEntry(entryId);
                            });
                        }
                    });
                })
                .catch(error => console.error('Error loading storage content:', error));
        }

        function deleteEntry(entryId) {
            if (confirm('Are you sure you want to delete this entry?')) {
                fetch('storage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'delete',
                        entry_id: entryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.status === 'success') {
                        loadStorageContent(); // Reload the content to reflect the deletion
                    }
                })
                .catch(error => console.error('Error deleting entry:', error));
            }
        }




        // Load Inventory Content
        function loadInventoryContent() {
            fetch('inventory.php?action=view')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('inventoryContent').innerHTML = data;
                })
                .catch(error => console.error('Error loading inventory content:', error));
        }
        async function loadPieChart() {
            const response = await fetch('storage.php?action=chart_data');
            const data = await response.json();

            if (data.status === 'success') {
                const labels = data.data.map(item => item.harvest_type);
                const quantities = data.data.map(item => item.total_quantity);

                const ctx = document.getElementById('inventorychart').getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: quantities,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.6)',
                                'rgba(54, 162, 235, 0.6)',
                                'rgba(255, 206, 86, 0.6)',
                                'rgba(75, 192, 192, 0.6)',
                                'rgba(153, 102, 255, 0.6)',
                                'rgba(255, 159, 64, 0.6)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (tooltipItem) {
                                        const value = tooltipItem.raw;
                                        return `${tooltipItem.label}: ${value} units`;
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.error('Failed to load chart data:', data.message);
            }
        }
 
        async function loadSiloCapacities() {
            const response = await fetch('storage.php?action=silo_capacity');
            const data = await response.json();

            if (data.status === 'success') {
                const tableBody = document.querySelector('#siloCapacityTable tbody');
                tableBody.innerHTML = ''; // Clear existing rows

                data.data.forEach(capacity => {
                    const row = `
                        <tr>
                            <td>${capacity.silo_location}</td>
                            <td>${capacity.remaining_capacity} kg</td>
                        </tr>
                    `;
                    tableBody.innerHTML += row;
                });
            } else {
                console.error('Failed to load silo capacities:', data.message);
            }
        }
       // Fetch prediction data
        fetch('predict.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                const labels = Object.keys(data.forecast);
                const values = Object.values(data.forecast);

                // Render chart
                const ctx = document.getElementById('predictionChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels.map(date => {
                            const options = { day: '2-digit', month: 'short' };
                            return new Date(date).toLocaleDateString('en-US', options);
                        }),
                        datasets: [{
                            label: 'Predicted Inventory Levels',
                            data: values,
                            borderWidth: 2,
                            borderColor: 'green',
                            backgroundColor: 'rgba(0, 255, 0, 0.2)',
                            fill: true,
                        }]
                    },
                    options: {
                        responsive: true,
                        layout: {
                            padding: {
                                top: 20,
                                bottom: 20,
                                left: 20,
                                right: 20
                            }
                        },
                        plugins: {
                            legend: {
                                labels: {
                                    font: {
                                        size: 12 // Adjust legend font size
                                    }
                                }
                            },
                            tooltip: {
                                titleFont: { size: 12 },
                                bodyFont: { size: 10 }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    font: {
                                        size: 10 // Smaller font for x-axis
                                    },
                                    maxRotation: 30, // Rotate labels slightly
                                    minRotation: 30,
                                    autoSkip: true,
                                    maxTicksLimit: 10, // Show fewer labels
                                    padding: 10
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 10 // Smaller font for y-axis
                                    },
                                    beginAtZero: true
                                }
                            }
                        }
                    }
                });
            } else {
                console.error(data.message);
            }
        })
        .catch(error => console.error('Error fetching predictions:', error));

        
        
    

        // Handle form submission (AJAX request)
        document.getElementById('dispatch-form').addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);

            fetch('dispatch.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const messageDiv = document.getElementById('dispatch-message');
                if (data.status === 'success') {
                    messageDiv.style.color = 'green';
                } else {
                    messageDiv.style.color = 'red';
                }
                messageDiv.textContent = data.message;

                // Optionally reset the form after successful dispatch
                if (data.status === 'success') {
                    document.getElementById('dispatch-form').reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('dispatch-message').textContent = 'An error occurred. Please try again.';
                document.getElementById('dispatch-message').style.color = 'red';
            });
        });
        document.getElementById('generate-report-button').addEventListener('click', function () {
            const formData = new FormData();
            formData.append('action', 'generate_report');

            fetch('dispatch.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => {
                if (response.ok) {
                    return response.blob(); // Get the response as a blob
                } else {
                    throw new Error('Failed to generate report.');
                }
            })
            .then(blob => {
                // Create a link to download the blob
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = 'dispatch_report.csv';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url); // Clean up
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to download dispatch report.');
            });
        });

        // Function to load dispatch records
        /*function loadDispatchTable() {
            fetch('dispatch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'view_dispatch' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('dispatchTable').innerHTML = data.data; // Insert table HTML
                } else {
                    document.getElementById('dispatchTable').innerHTML = `<p style="color: red;">${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching dispatch records:', error);
                document.getElementById('dispatchTable').innerHTML = '<p style="color: red;">Failed to load dispatch records.</p>';
            });
        }

        // Ensure the table is loaded when the dispatch section is displayed
        document.addEventListener('DOMContentLoaded', () => {
            const dispatchNavLink = document.querySelector('a[href="#dispatch-section"]');
            dispatchNavLink.addEventListener('click', loadDispatchTable);
        });*/
        function loadDispatchTable() {
            fetch('dispatch.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'view_dispatch' })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    const dispatchTableContainer = document.getElementById('dispatchTable');
                    dispatchTableContainer.innerHTML = data.data; // Insert table HTML

                    // Destroy existing DataTable instance if it exists
                    if ($.fn.DataTable.isDataTable('.styled-table')) {
                        $('.styled-table').DataTable().destroy();
                    }

                    // Initialize DataTable
                    $('.styled-table').DataTable({
                        scrollY: '400px',
                        scrollCollapse: true,
                        paging: true
                    });
                } else {
                    throw new Error(data.message || 'Failed to load dispatch records.');
                }
            })
            .catch(error => {
                console.error('Error fetching dispatch records:', error);
                document.getElementById('dispatchTable').innerHTML = `<p style="color: red;">${error.message}</p>`;
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const dispatchNavLink = document.querySelector('a[href="#dispatch-section"]');
            dispatchNavLink.addEventListener('click', loadDispatchTable);
        })


   

        // Initialize with default section
        document.addEventListener('DOMContentLoaded', () => {
            loadStorageContent();
            loadPieChart();
            loadSiloCapacities();
        });
        
    </script>
</body>
</html>

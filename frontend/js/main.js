// Sample users data for login
const users = [
    { username: 'admin', password: 'admin123' },
    { username: 'user1', password: 'password1' }
];

// Sample data for inventory
const inventoryItems = [
    { name: 'Corn', quantity: 100, dateAdded: '2023-10-01' },
    { name: 'Wheat', quantity: 75, dateAdded: '2023-10-05' },
];

// Sample data for suppliers
const suppliers = [
    { name: 'Green Farms', contact: 'green@farms.com', address: '123 Farm Lane' },
    { name: 'Harvest Co.', contact: 'contact@harvestco.com', address: '456 Harvest Street' }
];

// Populate Inventory List
function populateInventory() {
    const inventoryList = document.getElementById('inventoryList');
    inventoryList.innerHTML = ""; // Clear existing items
    inventoryItems.forEach((item, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>${item.dateAdded}</td>
            <td>
                <button onclick="editItem(${index})">Edit</button>
                <button onclick="deleteItem(${index})">Delete</button>
            </td>`;
        inventoryList.appendChild(row);
    });
}

// Edit Item Functionality
function editItem(index) {
    const item = inventoryItems[index];
    const newName = prompt("Enter new name", item.name);
    const newQuantity = prompt("Enter new quantity", item.quantity);
    if (newName && newQuantity) {
        inventoryItems[index] = { name: newName, quantity: newQuantity, dateAdded: item.dateAdded };
        populateInventory();
    }
}

// Delete Item Functionality
function deleteItem(index) {
    if (confirm("Are you sure you want to delete this item?")) {
        inventoryItems.splice(index, 1);
        populateInventory();
    }
}

// Handle Login Form Submission
document.getElementById('loginForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const username = this.username.value;
    const password = this.password.value;

    const user = users.find(user => user.username === username && user.password === password);
    if (user) {
        alert('Login successful!');
        window.location.href = 'dashboard.html'; // Redirect to dashboard
    } else {
        alert('Invalid username or password.');
    }
});

// Handle Signup Form Submission
document.getElementById('signupForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const name = this.name.value;
    const username = this.username.value;
    const password = this.password.value;
    const phone = this.phone.value;

    // Here you would usually send the signup data to your server
    alert(`Signup successful for user: ${username}`);
    window.location.href = 'login.html'; // Redirect to login
});

// Populate Suppliers List
function populateSuppliers() {
    const supplierList = document.getElementById('supplierList');
    supplierList.innerHTML = ""; // Clear existing entries
    suppliers.forEach((supplier, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${supplier.name}</td>
            <td>${supplier.contact}</td>
            <td>${supplier.address}</td>
            <td>
                <button onclick="editSupplier(${index})">Edit</button>
                <button onclick="deleteSupplier(${index})">Delete</button>
            </td>`;
        supplierList.appendChild(row);
    });
}

// Function to handle adding a new supplier
document.getElementById('supplierForm')?.addEventListener('submit', function(event) {
    event.preventDefault();
    const name = document.getElementById('supplierName').value;
    const contact = document.getElementById('supplierContact').value;
    const address = document.getElementById('supplierAddress').value;

    if (name && contact && address) {
        suppliers.push({ name, contact, address });
        populateSuppliers();
        alert('Supplier added successfully!');
        this.reset(); // Reset form fields
    } else {
        alert('Please fill in all fields.');
    }
});

// Function to edit a supplier
function editSupplier(index) {
    const supplier = suppliers[index];
    const newName = prompt("Edit Supplier Name", supplier.name);
    const newContact = prompt("Edit Contact Information", supplier.contact);
    const newAddress = prompt("Edit Address", supplier.address);

    if (newName && newContact && newAddress) {
        suppliers[index] = { name: newName, contact: newContact, address: newAddress };
        populateSuppliers();
    }
}

// Function to delete a supplier
function deleteSupplier(index) {
    if (confirm("Are you sure you want to delete this supplier?")) {
        suppliers.splice(index, 1);
        populateSuppliers();
    }
}

// Populate suppliers list on page load
if (document.getElementById('supplierList')) {
    populateSuppliers();
}

// Report generation placeholder
document.getElementById('generateReport')?.addEventListener('click', function() {
    const reportType = document.getElementById('reportType').value;
    document.getElementById('reportOutput').innerHTML = `Generated a ${reportType} report!`;
});

// Execute populate function if on inventory page
if (document.getElementById('inventoryList')) {
    populateInventory();
}

// Smooth scrolling for navigation links
document.querySelectorAll('nav a').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        document.querySelector(targetId).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Function to add an inventory item (placeholder)
function addItem() {
    const inventoryList = document.getElementById('inventory-list');
    const newItem = document.createElement('div');
    newItem.textContent = "New Inventory Item";
    inventoryList.appendChild(newItem);
}


// Sample data
const quickStats = {
    totalItems: 150,
    activeSuppliers: 5,
    harvestsThisMonth: 20,
    storageUtilization: 75
};

const recentActivity = [
    "Added new harvest item: Corn - 100 kg",
    "Updated storage capacity for Wheat",
    "New supplier added: Farm Fresh Ltd."
];

const alerts = [
    "Low stock alert for Rice",
    "Storage nearing capacity for Storage B"
];

// Load Quick Stats
function loadQuickStats() {
    document.getElementById('totalItems').textContent = quickStats.totalItems;
    document.getElementById('activeSuppliers').textContent = quickStats.activeSuppliers;
    document.getElementById('harvestsThisMonth').textContent = quickStats.harvestsThisMonth;
    document.getElementById('storageUtilization').textContent = `${quickStats.storageUtilization}%`;
}

// Load Recent Activity
function loadRecentActivity() {
    const recentActivityList = document.getElementById('recentActivityList');
    recentActivity.forEach(activity => {
        const listItem = document.createElement('li');
        listItem.textContent = activity;
        recentActivityList.appendChild(listItem);
    });
}

// Load Alerts & Notifications
function loadAlerts() {
    const alertsList = document.getElementById('alertsList');
    alerts.forEach(alert => {
        const listItem = document.createElement('li');
        listItem.textContent = alert;
        alertsList.appendChild(listItem);
    });
}

// Placeholder functions for Quick Actions
function addNewHarvest() {
    alert("Add New Harvest action triggered!");
}

function generateReport() {
    alert("Generate Report action triggered!");
}

function sendSupplierUpdate() {
    alert("Send Supplier Update action triggered!");
}

// Load all data when page loads
window.onload = function() {
    loadQuickStats();
    loadRecentActivity();
    loadAlerts();
};



















<?php $__env->startSection('title', 'Admin Dashboard | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>
<!-- Toastify -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<div class="min-h-screen flex flex-col md:flex-row" id="dashboard-container" style="display: none;">
  <!-- Left Sidebar -->
  <aside class="w-full md:w-64 bg-slate-900 text-slate-100 flex flex-col shrink-0">
    <div class="p-4 border-b border-slate-800 flex items-center justify-between">
      <a href="/" class="bg-white rounded-xl p-2 flex items-center justify-center w-full">
        <img src="/logo.jpeg" alt="Meditrust Nepal Logo" class="h-10 w-auto object-contain">
      </a>
      <button id="mobile-menu-toggle" class="md:hidden text-slate-100 hover:text-white p-2">
        <i class="fa-solid fa-bars text-xl"></i>
      </button>
    </div>

    <nav class="flex-1 p-4 space-y-1 overflow-y-auto" id="sidebar-nav">
      <a href="#overview" id="tab-link-overview" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-chart-line mr-3 w-5 text-center"></i> Dashboard
      </a>
      <a href="#products" id="tab-link-products" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-box mr-3 w-5 text-center"></i> Products
      </a>
      <a href="#orders" id="tab-link-orders" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-file-invoice mr-3 w-5 text-center"></i> Orders
      </a>
      <a href="#quotes" id="tab-link-quotes" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-envelope mr-3 w-5 text-center"></i> Quotes
      </a>
      <a href="#blog" id="tab-link-blog" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-blog mr-3 w-5 text-center"></i> Blog
      </a>
      <a href="#subscribers" id="tab-link-subscribers" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-bell mr-3 w-5 text-center"></i> Subscribers
      </a>
      <a href="#gbp" id="tab-link-gbp" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-brands fa-google mr-3 w-5 text-center"></i> Google Business
      </a>
      
      <!-- CMS Accordion -->
      <div class="pt-2">
        <button id="cms-menu-toggle" class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-slate-400 hover:bg-slate-800 hover:text-white transition-colors">
          <span class="flex items-center">
            <i class="fa-solid fa-file-lines mr-3 w-5 text-center"></i> Pages CMS
          </span>
          <i id="cms-chevron" class="fa-solid fa-chevron-right text-xs"></i>
        </button>
        <div id="cms-submenu" class="hidden pl-8 space-y-1 mt-1 border-l border-slate-800 ml-6">
          <a href="#cms-homepage" id="tab-link-cms-homepage" class="block px-3 py-2 rounded-lg text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-all">Homepage</a>
          <a href="#cms-services" id="tab-link-cms-services" class="block px-3 py-2 rounded-lg text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-all">Services Page</a>
          <a href="#cms-about" id="tab-link-cms-about" class="block px-3 py-2 rounded-lg text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-all">About Page</a>
          <a href="#cms-legal" id="tab-link-cms-legal" class="block px-3 py-2 rounded-lg text-xs text-slate-400 hover:bg-slate-800 hover:text-white transition-all">Legal Pages</a>
        </div>
      </div>

      <a href="#settings" id="tab-link-settings" class="flex items-center px-4 py-3 rounded-lg text-sm font-medium transition-colors">
        <i class="fa-solid fa-gear mr-3 w-5 text-center"></i> Settings
      </a>
    </nav>

    <!-- Sidebar Bottom Profile -->
    <div class="p-4 border-t border-slate-800">
      <div class="mb-3 px-2">
        <p class="text-xs text-slate-400 font-medium">Logged in as:</p>
        <p class="text-sm text-white font-semibold truncate" id="user-email">...</p>
        <span class="inline-block mt-1 text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400">Admin</span>
      </div>
      <a href="/" class="flex items-center px-3 py-2 text-xs text-slate-400 hover:text-white transition-colors mb-1 rounded hover:bg-slate-800">
        <i class="fa-solid fa-eye mr-2"></i> View Website
      </a>
      <button id="logout-btn" class="flex items-center px-3 py-2 text-xs text-red-400 hover:text-red-300 transition-colors w-full rounded hover:bg-red-500/10">
        <i class="fa-solid fa-right-from-bracket mr-2"></i> Logout
      </button>
    </div>
  </aside>

  <!-- Main Section -->
  <main class="flex-1 bg-slate-50 flex flex-col min-h-screen overflow-x-hidden">
    <!-- Topbar -->
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between">
      <h2 class="text-xl font-bold text-slate-800" id="current-tab-title">Dashboard Overview</h2>
      <div class="flex items-center gap-4">
        <!-- Date indicator -->
        <span class="text-xs text-slate-500 font-medium bg-slate-100 px-3 py-1.5 rounded-lg">
          <i class="fa-regular fa-calendar mr-1"></i> <span id="current-date"></span>
        </span>
      </div>
    </header>

    <!-- Tab Sections Container -->
    <div class="p-6 flex-1">
      
      <!-- ================= OVERVIEW TAB ================= -->
      <section id="tab-section-overview" class="tab-section hidden space-y-6">
        <!-- Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Products</p>
              <h3 class="text-3xl font-bold text-slate-800 mt-1" id="stat-products">0</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
              <i class="fa-solid fa-box"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Orders</p>
              <h3 class="text-3xl font-bold text-slate-800 mt-1" id="stat-orders">0</h3>
            </div>
            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">
              <i class="fa-solid fa-file-invoice"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Total Quotes</p>
              <h3 class="text-3xl font-bold text-slate-800 mt-1" id="stat-quotes">0</h3>
            </div>
            <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">
              <i class="fa-solid fa-envelope"></i>
            </div>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-slate-500">Subscribers</p>
              <h3 class="text-3xl font-bold text-slate-800 mt-1" id="stat-subscribers">0</h3>
            </div>
            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl">
              <i class="fa-solid fa-bell"></i>
            </div>
          </div>
        </div>

        <!-- Profit Analytics & Top Sellers -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Profit summary -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 lg:col-span-2 space-y-6">
            <h4 class="text-lg font-bold text-slate-800 flex items-center gap-2">
              <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
              Profit Analytics (NPR)
            </h4>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
              <div class="bg-emerald-50/50 p-4 rounded-xl border border-emerald-100">
                <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Total Revenue</p>
                <p class="text-2xl font-bold text-emerald-950 mt-1" id="profit-revenue">0</p>
              </div>
              <div class="bg-red-50/50 p-4 rounded-xl border border-red-100">
                <p class="text-xs font-semibold text-red-800 uppercase tracking-wider">Total Cost</p>
                <p class="text-2xl font-bold text-red-950 mt-1" id="profit-cost">0</p>
              </div>
              <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 col-span-2 sm:col-span-1">
                <p class="text-xs font-semibold text-blue-800 uppercase tracking-wider">Net Profit</p>
                <p class="text-2xl font-bold text-blue-950 mt-1" id="profit-net">0</p>
              </div>
            </div>
            <div class="border-t border-slate-100 pt-4 flex justify-between text-sm text-slate-500">
              <span>Avg. Profit Margin: <strong class="text-slate-800" id="profit-margin">0%</strong></span>
            </div>
          </div>
          
          <!-- Top Products -->
          <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100">
            <h4 class="text-lg font-bold text-slate-800 mb-4">Top Profitable Products</h4>
            <div class="space-y-3" id="top-products-list">
              <!-- Dynamically populated -->
            </div>
          </div>
        </div>

        <!-- Recent Orders table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-lg font-bold text-slate-800">Recent Order Inquiries</h4>
            <a href="#orders" class="text-xs font-semibold text-primary-600 hover:text-primary-700">View All Orders &rarr;</a>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">Order ID</th>
                  <th class="p-4">Customer</th>
                  <th class="p-4">Total</th>
                  <th class="p-4">Status</th>
                  <th class="p-4">Date</th>
                </tr>
              </thead>
              <tbody id="recent-orders-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= PRODUCTS TAB ================= -->
      <section id="tab-section-products" class="tab-section hidden space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div class="flex gap-2 w-full sm:w-auto">
            <input type="text" id="product-search" placeholder="Search products..." class="px-4 py-2.5 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary-500 outline-none w-full sm:w-64">
            <select id="product-cat-filter" class="px-3 py-2.5 rounded-lg border border-slate-200 text-sm focus:ring-2 focus:ring-primary-500 outline-none">
              <option value="">All Categories</option>
            </select>
          </div>
          <div class="flex items-center gap-2">
            <button onclick="exportProductsCSV()" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition-all">
              <i class="fa-solid fa-download mr-1.5"></i> Export CSV
            </button>
            <button onclick="triggerCSVImport()" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition-all">
              <i class="fa-solid fa-upload mr-1.5"></i> Import CSV
            </button>
            <button onclick="openProductModal()" class="px-4 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold shadow-md shadow-primary-500/20 transition-all">
              <i class="fa-solid fa-plus mr-1.5"></i> Add Product
            </button>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">Image</th>
                  <th class="p-4">Name</th>
                  <th class="p-4">Category</th>
                  <th class="p-4">Price</th>
                  <th class="p-4">Cost</th>
                  <th class="p-4">Stock</th>
                  <th class="p-4">Featured</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="products-table-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
          
          <!-- Pagination -->
          <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs font-medium text-slate-500">
            <span id="product-pagination-info">Showing page 1 of 1</span>
            <div class="flex gap-1">
              <button id="product-prev-btn" class="px-3 py-1.5 border border-slate-200 rounded hover:bg-slate-50">Prev</button>
              <button id="product-next-btn" class="px-3 py-1.5 border border-slate-200 rounded hover:bg-slate-50">Next</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ================= ORDERS TAB ================= -->
      <section id="tab-section-orders" class="tab-section hidden space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">ID</th>
                  <th class="p-4">Customer Details</th>
                  <th class="p-4">Items Summary</th>
                  <th class="p-4">Total Price</th>
                  <th class="p-4">Status</th>
                  <th class="p-4">Date</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="orders-table-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= QUOTES TAB ================= -->
      <section id="tab-section-quotes" class="tab-section hidden space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-bold text-slate-800">Direct Quote Inquiries</h3>
          <button onclick="exportQuotesCSV()" class="px-4 py-2.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-sm font-semibold transition-all">
            <i class="fa-solid fa-download mr-1.5"></i> Export Quotes
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">Name</th>
                  <th class="p-4">Hospital</th>
                  <th class="p-4">Phone / Email</th>
                  <th class="p-4">Product Name</th>
                  <th class="p-4">Qty</th>
                  <th class="p-4">Status</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="quotes-table-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= BLOG TAB ================= -->
      <section id="tab-section-blog" class="tab-section hidden space-y-6">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-bold text-slate-800">Articles & News</h3>
          <button onclick="openBlogModal()" class="px-4 py-2.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold shadow-md transition-all">
            <i class="fa-solid fa-plus mr-1.5"></i> Add Article
          </button>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">Banner</th>
                  <th class="p-4">Title</th>
                  <th class="p-4">Category</th>
                  <th class="p-4">Author</th>
                  <th class="p-4">Date</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="blog-table-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= SUBSCRIBERS TAB ================= -->
      <section id="tab-section-subscribers" class="tab-section hidden space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
          <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
              <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100">
                  <th class="p-4">Name</th>
                  <th class="p-4">Phone</th>
                  <th class="p-4">Source</th>
                  <th class="p-4">Signup Date</th>
                  <th class="p-4 text-right">Actions</th>
                </tr>
              </thead>
              <tbody id="subscribers-table-rows" class="text-sm divide-y divide-slate-100">
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ================= GBP TAB ================= -->
      <section id="tab-section-gbp" class="tab-section hidden space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
          <h4 class="text-lg font-bold text-slate-800">Google Business Profile Integration</h4>
          <p class="text-sm text-slate-500">Sync Google Business reviews and post announcements directly from this dashboard.</p>
          
          <div id="gbp-status-box" class="p-4 rounded-xl border flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Populated dynamically -->
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Google reviews -->
            <div class="border border-slate-100 rounded-xl p-4">
              <h5 class="font-bold text-slate-700 mb-3"><i class="fa-solid fa-star text-amber-400 mr-1.5"></i> Google Business Reviews</h5>
              <div id="gbp-reviews" class="space-y-4 max-h-[400px] overflow-y-auto">
                <p class="text-slate-400 text-xs">Connect Google account to sync reviews.</p>
              </div>
            </div>
            <!-- Google Posts -->
            <div class="border border-slate-100 rounded-xl p-4">
              <h5 class="font-bold text-slate-700 mb-3"><i class="fa-regular fa-newspaper text-blue-500 mr-1.5"></i> Post to Google Business</h5>
              <form id="gbp-post-form" class="space-y-3">
                <textarea id="gbp-post-text" placeholder="Write an update, offer, or event to publish to Google Business..." rows="4" class="w-full text-sm border rounded-lg p-3 outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold">Publish Post</button>
              </form>
            </div>
          </div>
        </div>
      </section>

      <!-- ================= CMS TABS ================= -->
      <section id="tab-section-cms-homepage" class="tab-section hidden space-y-6">
        <form id="cms-homepage-form" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
          <h4 class="text-lg font-bold text-slate-800">Edit Homepage Content</h4>
          <div id="cms-homepage-fields" class="space-y-4"></div>
          <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Changes</button>
        </form>
      </section>

      <section id="tab-section-cms-services" class="tab-section hidden space-y-6">
        <form id="cms-services-form" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
          <h4 class="text-lg font-bold text-slate-800">Edit Services Settings</h4>
          <div id="cms-services-fields" class="space-y-4"></div>
          <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Changes</button>
        </form>
      </section>

      <section id="tab-section-cms-about" class="tab-section hidden space-y-6">
        <form id="cms-about-form" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
          <h4 class="text-lg font-bold text-slate-800">Edit About Us Settings</h4>
          <div id="cms-about-fields" class="space-y-4"></div>
          <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Changes</button>
        </form>
      </section>

      <section id="tab-section-cms-legal" class="tab-section hidden space-y-6">
        <form id="cms-legal-form" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 space-y-6">
          <h4 class="text-lg font-bold text-slate-800">Edit Legal Pages (Privacy Policy, T&C)</h4>
          <div id="cms-legal-fields" class="space-y-4"></div>
          <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Changes</button>
        </form>
      </section>

      <!-- ================= SETTINGS TAB ================= -->
      <section id="tab-section-settings" class="tab-section hidden space-y-6">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 max-w-xl">
          <h4 class="text-lg font-bold text-slate-800 mb-6">Profile Settings</h4>
          
          <form id="profile-form" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Name</label>
              <input type="text" id="settings-name" required class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
              <input type="text" id="settings-username" required class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
              <input type="email" id="settings-email" required class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="border-t border-slate-100 pt-4">
              <h5 class="text-sm font-bold text-slate-700 mb-3">Change Password (Optional)</h5>
              <div class="space-y-3">
                <input type="password" id="settings-current-pass" placeholder="Current Password" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary-500">
                <input type="password" id="settings-new-pass" placeholder="New Password" class="w-full px-4 py-2 border border-slate-200 rounded-lg outline-none focus:ring-2 focus:ring-primary-500">
              </div>
            </div>
            <button type="submit" class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold transition-all text-sm mt-4">Save Profile Changes</button>
          </form>
        </div>
      </section>

    </div>
  </main>
</div>

<!-- ================= PRODUCT MODAL ================= -->
<div id="product-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 overflow-y-auto">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
      <h3 class="text-lg font-bold text-slate-800" id="product-modal-title">Add Product</h3>
      <button onclick="closeProductModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
    </div>
    
    <form id="product-form" class="p-6 space-y-6">
      <input type="hidden" id="product-edit-id">
      
      <!-- Basic Tab layout in Modal -->
      <div class="flex border-b border-slate-100 gap-6 text-sm mb-4">
        <button type="button" onclick="switchProductFormTab('basic')" id="prod-tab-basic" class="pb-3 border-b-2 border-primary-600 font-semibold text-primary-600">Basic Info</button>
        <button type="button" onclick="switchProductFormTab('pricing')" id="prod-tab-pricing" class="pb-3 border-b-2 border-transparent font-medium text-slate-500 hover:text-slate-700">Pricing & Stock</button>
        <button type="button" onclick="switchProductFormTab('seo')" id="prod-tab-seo" class="pb-3 border-b-2 border-transparent font-medium text-slate-500 hover:text-slate-700">SEO & Badges</button>
      </div>

      <!-- Basic Info Tab -->
      <div id="prod-section-basic" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Product Name</label>
            <input type="text" id="prod-name" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category</label>
            <input type="text" id="prod-category" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="e.g. ICU Equipment">
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Product Slug</label>
          <div class="flex gap-2">
            <input type="text" id="prod-slug" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="seo-friendly-slug">
            <button type="button" onclick="generateAISlugAndSEO()" id="ai-generate-btn" class="px-4 py-2 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white rounded-lg text-xs font-bold flex items-center gap-1.5 transition-all">
              <i class="fa-solid fa-wand-magic-sparkles"></i> AI SEO
            </button>
          </div>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Description</label>
          <textarea id="prod-description" rows="5" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Specifications</label>
          <textarea id="prod-specifications" rows="4" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Dimension: 20x10... Warranty: 1 Year..."></textarea>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Upload Product Images</label>
          <input type="file" id="prod-images" multiple accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
        </div>
      </div>

      <!-- Pricing & Stock Tab -->
      <div id="prod-section-pricing" class="space-y-4 hidden">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Selling Price (NPR)</label>
            <input type="number" id="prod-price" required min="0" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Original Price (NPR - Optional)</label>
            <input type="number" id="prod-original-price" min="0" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
          </div>
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Dealer/Internal Cost (NPR)</label>
            <input type="number" id="prod-cost" min="0" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
          </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Available Stock Qty</label>
            <input type="number" id="prod-stock" min="0" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
          </div>
          <div class="flex items-center pt-6">
            <input type="checkbox" id="prod-featured" class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
            <label for="prod-featured" class="ml-2 text-sm font-medium text-slate-700">Feature this product on homepage</label>
          </div>
        </div>
      </div>

      <!-- SEO & Badges Tab -->
      <div id="prod-section-seo" class="space-y-4 hidden">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Meta Title</label>
          <input type="text" id="prod-meta-title" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Meta Description</label>
          <textarea id="prod-meta-desc" rows="2" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Meta Keywords</label>
          <input type="text" id="prod-meta-keywords" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="e.g. ICU, Medical Equipment Nepal">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Badges (Separate with pipe | )</label>
          <input type="text" id="prod-badges" class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="CE & ISO Certified | 1 Year Warranty | Free Delivery">
        </div>
      </div>

      <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
        <button type="button" onclick="closeProductModal()" class="px-4 py-2.5 border rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-all">Cancel</button>
        <button type="submit" id="product-save-btn" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Product</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= BLOG MODAL ================= -->
<div id="blog-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 overflow-y-auto">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between sticky top-0 bg-white">
      <h3 class="text-lg font-bold text-slate-800" id="blog-modal-title">Add Article</h3>
      <button onclick="closeBlogModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
    </div>
    
    <form id="blog-form" class="p-6 space-y-4">
      <input type="hidden" id="blog-edit-id">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Title</label>
        <input type="text" id="blog-title" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Slug</label>
        <input type="text" id="blog-slug" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="seo-article-slug">
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Category</label>
          <input type="text" id="blog-category" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none" placeholder="Medical Insights">
        </div>
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Author</label>
          <input type="text" id="blog-author" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none">
        </div>
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Excerpt / Summary</label>
        <textarea id="blog-excerpt" rows="2" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Content (Markdown/HTML supported)</label>
        <textarea id="blog-content" rows="8" required class="w-full px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-primary-500 outline-none"></textarea>
      </div>
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Featured Banner Image</label>
        <input type="file" id="blog-image" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
      </div>
      
      <div class="p-6 border-t border-slate-100 flex justify-end gap-3 bg-slate-50 rounded-b-2xl">
        <button type="button" onclick="closeBlogModal()" class="px-4 py-2.5 border rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-all">Cancel</button>
        <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg text-sm transition-all">Save Article</button>
      </div>
    </form>
  </div>
</div>

<!-- ================= CSV IMPORT MODAL ================= -->
<div id="csv-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
      <h3 class="text-lg font-bold text-slate-800">Import Products CSV</h3>
      <button onclick="closeCSVModal()" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-xl"></i></button>
    </div>
    <form id="csv-form" class="p-6 space-y-4">
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Select CSV File</label>
        <input type="file" id="csv-file" accept=".csv" required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
      </div>
      <div class="flex justify-end gap-2 pt-4">
        <button type="button" onclick="closeCSVModal()" class="px-4 py-2 border rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-100">Cancel</button>
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-bold">Upload & Import</button>
      </div>
    </form>
  </div>
</div>

<script>
// Authentication check
async function checkAuth() {
  try {
    const res = await fetch('/api/v1/auth/me', {
      headers: { 'Accept': 'application/json' }
    });
    if (res.status === 401) {
      window.location.href = '/admin/login?returnTo=' + encodeURIComponent(window.location.pathname + window.location.search);
      return;
    }
    const data = await res.json();
    if (!data.user || data.user.role !== 'admin') {
      window.location.href = '/admin/login';
      return;
    }
    document.getElementById('user-email').textContent = data.user.email;
    document.getElementById('dashboard-container').style.display = 'flex';
  } catch (err) {
    console.error(err);
    window.location.href = '/admin/login';
  }
}
checkAuth();

// Date setup
document.getElementById('current-date').textContent = new Date().toLocaleDateString('en-US', {
  weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
});

// Toast Helper
function notify(text, type = 'success') {
  Toastify({
    text: text,
    duration: 3500,
    close: true,
    gravity: "bottom",
    position: "right",
    style: {
      background: type === 'success' ? '#10B981' : (type === 'error' ? '#EF4444' : '#3B82F6')
    }
  }).showToast();
}

// Sidebar toggle on mobile
const mobileBtn = document.getElementById('mobile-menu-toggle');
const sidebar = document.querySelector('aside');
mobileBtn.addEventListener('click', () => {
  sidebar.classList.toggle('-translate-x-full');
});

// CMS sub-menu accordion toggle
const cmsToggleBtn = document.getElementById('cms-menu-toggle');
const cmsSubmenu = document.getElementById('cms-submenu');
const cmsChevron = document.getElementById('cms-chevron');
cmsToggleBtn.addEventListener('click', () => {
  cmsSubmenu.classList.toggle('hidden');
  cmsChevron.classList.toggle('fa-chevron-right');
  cmsChevron.classList.toggle('fa-chevron-down');
});

// SPA Tab Routing Setup
const tabs = ['overview', 'products', 'orders', 'quotes', 'blog', 'subscribers', 'gbp', 'cms-homepage', 'cms-services', 'cms-about', 'cms-legal', 'settings'];

function showTab(tabName) {
  if (!tabs.includes(tabName)) tabName = 'overview';
  
  // Update sidebar active classes
  tabs.forEach(t => {
    const link = document.getElementById(`tab-link-${t}`);
    const section = document.getElementById(`tab-section-${t}`);
    if (link) {
      if (t === tabName) {
        link.classList.add('bg-primary-600', 'text-white');
        link.classList.remove('text-slate-400', 'hover:bg-slate-800', 'hover:text-white');
      } else {
        link.classList.remove('bg-primary-600', 'text-white');
        link.classList.add('text-slate-400', 'hover:bg-slate-800', 'hover:text-white');
      }
    }
    if (section) {
      if (t === tabName) section.classList.remove('hidden');
      else section.classList.add('hidden');
    }
  });

  // Capitalize title
  const formattedTitle = tabName.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
  document.getElementById('current-tab-title').textContent = formattedTitle;

  // Collapse mobile sidebar when switching tabs
  if (window.innerWidth < 768) {
    sidebar.classList.add('-translate-x-full');
  }

  // Load appropriate data
  if (tabName === 'overview') loadOverview();
  else if (tabName === 'products') loadProducts();
  else if (tabName === 'orders') loadOrders();
  else if (tabName === 'quotes') loadQuotes();
  else if (tabName === 'blog') loadBlogs();
  else if (tabName === 'subscribers') loadSubscribers();
  else if (tabName === 'gbp') loadGbp();
  else if (tabName.startsWith('cms-')) loadCms(tabName);
  else if (tabName === 'settings') loadSettings();
}

window.addEventListener('hashchange', () => {
  const hash = window.location.hash.substring(1);
  showTab(hash);
});

document.addEventListener('DOMContentLoaded', () => {
  const hash = window.location.hash.substring(1) || 'overview';
  showTab(hash);
});

// Logout handler
document.getElementById('logout-btn').addEventListener('click', async () => {
  try {
    await fetch('/api/v1/auth/logout', { method: 'POST' });
    localStorage.removeItem('meditrust_was_logged_in');
    notify('Logged out successfully');
    window.location.href = '/admin/login';
  } catch (err) {
    notify('Logout failed', 'error');
  }
});

// ================= DATA LOADERS =================

// 1. OVERVIEW
async function loadOverview() {
  try {
    const [statsRes, analyticsRes] = await Promise.all([
      fetch('/api/v1/products?limit=1'), // to get totals
      fetch('/api/v1/analytics/dashboard')
    ]);
    
    const prodData = await statsRes.json();
    const analytics = await analyticsRes.json();
    
    document.getElementById('stat-products').textContent = prodData.total || 0;
    document.getElementById('stat-orders').textContent = analytics.summary?.totalOrders || 0;
    
    // Fetch quotes stats
    const quoteStatsRes = await fetch('/api/v1/quotes/stats');
    const quoteStats = await quoteStatsRes.json();
    document.getElementById('stat-quotes').textContent = quoteStats.total || 0;
    
    // Fetch subscribers count
    const subRes = await fetch('/api/v1/notify/subscribers');
    const subData = await subRes.json();
    document.getElementById('stat-subscribers').textContent = subData.total || 0;

    // Fill analytics profit banner
    document.getElementById('profit-revenue').textContent = 'NPR ' + (analytics.summary?.totalRevenue || 0).toLocaleString();
    document.getElementById('profit-cost').textContent = 'NPR ' + (analytics.summary?.totalCost || 0).toLocaleString();
    document.getElementById('profit-net').textContent = 'NPR ' + (analytics.summary?.totalProfit || 0).toLocaleString();
    document.getElementById('profit-margin').textContent = (analytics.summary?.profitMargin || 0) + '%';
    
    // Populate top products
    const topProdList = document.getElementById('top-products-list');
    topProdList.innerHTML = '';
    if (analytics.topProducts && analytics.topProducts.length > 0) {
      analytics.topProducts.slice(0, 5).forEach((p, idx) => {
        topProdList.innerHTML += `
          <div class="flex items-center justify-between text-xs py-1.5 border-b border-slate-50 last:border-0">
            <span class="text-slate-700 font-medium truncate max-w-[160px]">${idx + 1}. ${p.name || 'Unknown'}</span>
            <span class="font-bold text-slate-900">NPR ${p.profit?.toLocaleString() || 0}</span>
          </div>`;
      });
    } else {
      topProdList.innerHTML = '<p class="text-slate-400 text-xs">No analytics data available.</p>';
    }

    // Populate recent orders
    const ordersRows = document.getElementById('recent-orders-rows');
    ordersRows.innerHTML = '';
    if (analytics.recentOrders && analytics.recentOrders.length > 0) {
      analytics.recentOrders.forEach(o => {
        const orderDate = o.date ? new Date(o.date).toLocaleDateString() : 'N/A';
        let badgeColor = 'bg-slate-100 text-slate-600';
        if (o.status === 'confirmed') badgeColor = 'bg-blue-100 text-blue-700';
        else if (o.status === 'shipped') badgeColor = 'bg-amber-100 text-amber-700';
        else if (o.status === 'delivered') badgeColor = 'bg-green-100 text-green-700';
        else if (o.status === 'cancelled') badgeColor = 'bg-red-100 text-red-700';
        
        ordersRows.innerHTML += `
          <tr>
            <td class="p-4 font-bold text-primary-600">${o.id}</td>
            <td class="p-4 text-slate-600">Customer</td>
            <td class="p-4 font-semibold text-slate-800">NPR ${(o.total || 0).toLocaleString()}</td>
            <td class="p-4">
              <span class="px-2 py-0.5 rounded text-xs font-semibold ${badgeColor}">${o.status}</span>
            </td>
            <td class="p-4 text-slate-500">${orderDate}</td>
          </tr>`;
      });
    } else {
      ordersRows.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-slate-400">No orders found.</td></tr>';
    }
  } catch (err) {
    console.error('Failed to load overview', err);
  }
}

// 2. PRODUCTS
let currentProductPage = 1;
const productSearchInput = document.getElementById('product-search');
const productCatFilter = document.getElementById('product-cat-filter');

productSearchInput.addEventListener('input', () => { currentProductPage = 1; loadProducts(); });
productCatFilter.addEventListener('change', () => { currentProductPage = 1; loadProducts(); });

async function loadProducts() {
  try {
    const searchVal = productSearchInput.value.trim();
    const catVal = productCatFilter.value;
    
    let url = `/api/v1/products?page=${currentProductPage}&limit=10`;
    if (searchVal) url += `&search=${encodeURIComponent(searchVal)}`;
    if (catVal) url += `&category=${encodeURIComponent(catVal)}`;
    
    const res = await fetch(url);
    const data = await res.json();
    
    // Load categories filter dropdown only once if it is empty
    if (productCatFilter.options.length <= 1) {
      const allCatsRes = await fetch('/api/v1/products?limit=100');
      const allCatsData = await allCatsRes.json();
      const seenCats = new Set();
      (allCatsData.products || []).forEach(p => {
        if (p.category && !seenCats.has(p.category)) {
          seenCats.add(p.category);
          const opt = document.createElement('option');
          opt.value = p.category;
          opt.textContent = p.category;
          productCatFilter.appendChild(opt);
        }
      });
    }

    const tableRows = document.getElementById('products-table-rows');
    tableRows.innerHTML = '';
    
    if (data.products && data.products.length > 0) {
      data.products.forEach(p => {
        const image = p.image || '/assets/placeholder-image.jpg';
        const featuredBadge = p.featured 
          ? '<span class="px-2 py-0.5 rounded text-[10px] uppercase font-bold tracking-wider bg-purple-100 text-purple-700">Featured</span>'
          : '<span class="text-slate-300">-</span>';
          
        tableRows.innerHTML += `
          <tr>
            <td class="p-4">
              <img src="${image}" alt="${p.name}" class="h-10 w-10 object-cover rounded-lg border border-slate-100">
            </td>
            <td class="p-4 font-semibold text-slate-800">${p.name}</td>
            <td class="p-4 text-slate-500">${p.category}</td>
            <td class="p-4 font-bold text-slate-900">NPR ${(p.price || 0).toLocaleString()}</td>
            <td class="p-4 text-slate-600">NPR ${(p.cost || 0).toLocaleString()}</td>
            <td class="p-4 text-slate-600">${p.stock || 0}</td>
            <td class="p-4">${featuredBadge}</td>
            <td class="p-4 text-right space-x-2">
              <button onclick="editProduct('${p.id || p._id}')" class="text-blue-500 hover:text-blue-700 font-semibold text-xs"><i class="fa-solid fa-edit"></i> Edit</button>
              <button onclick="duplicateProduct(${JSON.stringify(p).replace(/"/g, '&quot;')})" class="text-amber-500 hover:text-amber-700 font-semibold text-xs"><i class="fa-solid fa-copy"></i> Copy</button>
              <button onclick="deleteProduct('${p.id || p._id}')" class="text-red-500 hover:text-red-700 font-semibold text-xs"><i class="fa-solid fa-trash"></i> Del</button>
            </td>
          </tr>`;
      });
    } else {
      tableRows.innerHTML = '<tr><td colspan="8" class="p-8 text-center text-slate-400">No products found.</td></tr>';
    }

    // Update pagination buttons
    document.getElementById('product-pagination-info').textContent = `Page ${data.page || 1} of ${data.pages || 1} (Total: ${data.total || 0})`;
    
    const prevBtn = document.getElementById('product-prev-btn');
    const nextBtn = document.getElementById('product-next-btn');
    
    prevBtn.disabled = currentProductPage <= 1;
    nextBtn.disabled = currentProductPage >= (data.pages || 1);
    
    prevBtn.onclick = () => { if (currentProductPage > 1) { currentProductPage--; loadProducts(); } };
    nextBtn.onclick = () => { if (currentProductPage < data.pages) { currentProductPage++; loadProducts(); } };
  } catch (err) {
    console.error(err);
  }
}

// AI SEO Filler
async function generateAISlugAndSEO() {
  const prodName = document.getElementById('prod-name').value.trim();
  const category = document.getElementById('prod-category').value.trim();
  const aiBtn = document.getElementById('ai-generate-btn');
  
  if (!prodName) {
    notify('Please enter a product name first', 'error');
    return;
  }
  
  aiBtn.disabled = true;
  aiBtn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i> Loading...';
  
  try {
    const prompt = `You are an SEO expert for a medical equipment supplier in Nepal called Meditrust Nepal.
Given this product: "${prodName}"${category ? ` (Category: ${category})` : ''}.
Generate the following in JSON format only, no markdown choices or explanations:
{
  "slug": "seo-friendly-url-slug-nepal",
  "metaTitle": "Product Name - Buy in Nepal | Meditrust Nepal",
  "metaDescription": "150-160 char description for Google",
  "metaKeywords": "product name Nepal, buy product Kathmandu, medical equipment Nepal",
  "description": "2-3 sentence product description mentioning Nepal, CE/ISO certified"
}`;

    const response = await fetch('/api/v1/ai/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        messages: [{ role: 'user', content: prompt }],
        temperature: 0.7,
        max_tokens: 400
      })
    });
    
    const data = await response.json();
    const aiText = data.choices?.[0]?.message?.content || '';
    const cleanJSONText = aiText.replace(/```json\n?|```\n?/g, '').trim();
    const parsed = JSON.parse(cleanJSONText);
    
    document.getElementById('prod-slug').value = parsed.slug || '';
    document.getElementById('prod-meta-title').value = parsed.metaTitle || '';
    document.getElementById('prod-meta-desc').value = parsed.metaDescription || '';
    document.getElementById('prod-meta-keywords').value = parsed.metaKeywords || '';
    document.getElementById('prod-description').value = parsed.description || '';
    
    notify('SEO and URL Slug generated successfully!');
  } catch (err) {
    console.error(err);
    notify('AI field generator failed. Please fill manually.', 'error');
  } finally {
    aiBtn.disabled = false;
    aiBtn.innerHTML = '<i class="fa-solid fa-wand-magic-sparkles"></i> AI SEO';
  }
}

// Product CSV Export
function exportProductsCSV() {
  window.open('/api/v1/products/export.csv');
}

// Quotes CSV Export
function exportQuotesCSV() {
  window.open('/api/v1/products/quotes-export.csv');
}

// Trigger CSV Import Modal
function triggerCSVImport() {
  document.getElementById('csv-modal').classList.remove('hidden');
}

function closeCSVModal() {
  document.getElementById('csv-modal').classList.add('hidden');
}

document.getElementById('csv-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const file = document.getElementById('csv-file').files[0];
  if (!file) return;
  
  const formData = new FormData();
  formData.append('file', file);
  
  try {
    const res = await fetch('/api/v1/products/import-csv', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    });
    if (res.ok) {
      notify('CSV imported successfully!');
      closeCSVModal();
      loadProducts();
    } else {
      const err = await res.json();
      notify(err.error || 'Failed to import CSV', 'error');
    }
  } catch (err) {
    notify('CSV import error', 'error');
  }
});

// Product Modals & Submissions
function openProductModal() {
  document.getElementById('product-form').reset();
  document.getElementById('product-edit-id').value = '';
  document.getElementById('product-modal-title').textContent = 'Add Product';
  switchProductFormTab('basic');
  document.getElementById('product-modal').classList.remove('hidden');
}

function closeProductModal() {
  document.getElementById('product-modal').classList.add('hidden');
}

function switchProductFormTab(tabName) {
  const sections = ['basic', 'pricing', 'seo'];
  sections.forEach(s => {
    const tabBtn = document.getElementById(`prod-tab-${s}`);
    const secDiv = document.getElementById(`prod-section-${s}`);
    if (s === tabName) {
      tabBtn.className = 'pb-3 border-b-2 border-primary-600 font-semibold text-primary-600';
      secDiv.classList.remove('hidden');
    } else {
      tabBtn.className = 'pb-3 border-b-2 border-transparent font-medium text-slate-500 hover:text-slate-700';
      secDiv.classList.add('hidden');
    }
  });
}

// Create/Update Product Submit
document.getElementById('product-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const id = document.getElementById('product-edit-id').value;
  const formData = new FormData();
  
  formData.append('name', document.getElementById('prod-name').value);
  formData.append('category', document.getElementById('prod-category').value);
  formData.append('slug', document.getElementById('prod-slug').value);
  formData.append('description', document.getElementById('prod-description').value);
  formData.append('specifications', document.getElementById('prod-specifications').value);
  formData.append('price', document.getElementById('prod-price').value);
  formData.append('originalPrice', document.getElementById('prod-original-price').value);
  formData.append('cost', document.getElementById('prod-cost').value);
  formData.append('stock', document.getElementById('prod-stock').value);
  formData.append('featured', document.getElementById('prod-featured').checked ? '1' : '0');
  formData.append('metaTitle', document.getElementById('prod-meta-title').value);
  formData.append('metaDescription', document.getElementById('prod-meta-desc').value);
  formData.append('metaKeywords', document.getElementById('prod-meta-keywords').value);
  
  const badgesVal = document.getElementById('prod-badges').value;
  if (badgesVal) {
    formData.append('badges', badgesVal);
  }
  
  const files = document.getElementById('prod-images').files;
  for (let i = 0; i < files.length; i++) {
    formData.append('images[]', files[i]);
  }
  
  const saveBtn = document.getElementById('product-save-btn');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';

  try {
    let url = '/api/v1/products';
    let method = 'POST';
    
    if (id) {
      url = `/api/v1/products/${id}`;
      // Laravel handles PUT with FormData best using POST and a _method field
      formData.append('_method', 'PUT');
    }
    
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    });
    
    const data = await res.json();
    if (res.ok) {
      notify(id ? 'Product updated' : 'Product created');
      closeProductModal();
      loadProducts();
    } else {
      notify(data.error || 'Failed to save product', 'error');
    }
  } catch (err) {
    notify('Error saving product', 'error');
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Product';
  }
});

// Edit product lookup
async function editProduct(id) {
  try {
    const res = await fetch(`/api/v1/products/${id}`);
    const p = await res.json();
    
    openProductModal();
    document.getElementById('product-modal-title').textContent = 'Edit Product';
    document.getElementById('product-edit-id').value = p.id || p._id;
    
    document.getElementById('prod-name').value = p.name || '';
    document.getElementById('prod-category').value = p.category || '';
    document.getElementById('prod-slug').value = p.slug || '';
    document.getElementById('prod-description').value = p.description || '';
    document.getElementById('prod-specifications').value = p.specifications || '';
    document.getElementById('prod-price').value = p.price || 0;
    document.getElementById('prod-original-price').value = p.originalPrice || '';
    document.getElementById('prod-cost').value = p.cost || 0;
    document.getElementById('prod-stock').value = p.stock || 0;
    document.getElementById('prod-featured').checked = !!p.featured;
    document.getElementById('prod-meta-title').value = p.meta_title || p.metaTitle || '';
    document.getElementById('prod-meta-desc').value = p.meta_description || p.metaDescription || '';
    document.getElementById('prod-meta-keywords').value = p.meta_keywords || p.metaKeywords || '';
    document.getElementById('prod-badges').value = (p.badges || []).join(' | ');
  } catch (err) {
    notify('Failed to load product details', 'error');
  }
}

// Duplicate product helper
function duplicateProduct(p) {
  openProductModal();
  document.getElementById('prod-name').value = (p.name || '') + ' (Copy)';
  document.getElementById('prod-category').value = p.category || '';
  document.getElementById('prod-slug').value = '';
  document.getElementById('prod-description').value = p.description || '';
  document.getElementById('prod-specifications').value = p.specifications || '';
  document.getElementById('prod-price').value = p.price || 0;
  document.getElementById('prod-original-price').value = p.originalPrice || '';
  document.getElementById('prod-cost').value = p.cost || 0;
  document.getElementById('prod-stock').value = p.stock || 0;
  document.getElementById('prod-featured').checked = false;
  document.getElementById('prod-badges').value = (p.badges || []).join(' | ');
}

// Delete product
async function deleteProduct(id) {
  if (!confirm('Are you sure you want to delete this product?')) return;
  try {
    const res = await fetch(`/api/v1/products/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    if (res.ok) {
      notify('Product deleted successfully');
      loadProducts();
    } else {
      notify('Delete failed', 'error');
    }
  } catch (err) {
    notify('Delete error', 'error');
  }
}

// 3. ORDERS
async function loadOrders() {
  try {
    const res = await fetch('/api/v1/orders');
    const data = await res.json();
    const rows = document.getElementById('orders-table-rows');
    rows.innerHTML = '';
    
    if (data.orders && data.orders.length > 0) {
      data.orders.forEach(o => {
        const orderDate = o.createdAt ? new Date(o.createdAt).toLocaleDateString() : 'N/A';
        const address = o.shipping_address || {};
        
        let itemsSummary = '';
        (o.items || []).forEach(item => {
          itemsSummary += `<div class="text-xs text-slate-600">${item.name} (x${item.quantity})</div>`;
        });
        
        const statusOptions = ['awaiting_payment', 'confirmed', 'shipped', 'delivered', 'cancelled'];
        let selectHtml = `<select onchange="updateOrderStatus('${o.id}', this.value)" class="text-xs border rounded px-2 py-1 outline-none">`;
        statusOptions.forEach(opt => {
          selectHtml += `<option value="${opt}" ${o.status === opt ? 'selected' : ''}>${opt}</option>`;
        });
        selectHtml += `</select>`;
        
        rows.innerHTML += `
          <tr>
            <td class="p-4 font-bold text-primary-600">${o.id}</td>
            <td class="p-4">
              <div class="font-semibold text-slate-800">${address.name || 'N/A'}</div>
              <div class="text-xs text-slate-500">${address.phone || ''}</div>
              <div class="text-xs text-slate-400 truncate max-w-[200px]">${address.addressLine || ''}, ${address.city || ''}</div>
            </td>
            <td class="p-4">${itemsSummary}</td>
            <td class="p-4 font-bold text-slate-900">NPR ${(o.total_price || 0).toLocaleString()}</td>
            <td class="p-4">${selectHtml}</td>
            <td class="p-4 text-slate-500">${orderDate}</td>
            <td class="p-4 text-right">
              <button onclick="deleteOrder('${o.id}')" class="text-red-500 hover:text-red-700 font-semibold text-xs"><i class="fa-solid fa-trash"></i> Delete</button>
            </td>
          </tr>`;
      });
    } else {
      rows.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-slate-400">No orders found.</td></tr>';
    }
  } catch (err) {
    console.error(err);
  }
}

async function updateOrderStatus(id, newStatus) {
  try {
    const res = await fetch(`/api/v1/orders/${id}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ status: newStatus })
    });
    if (res.ok) {
      notify('Order status updated');
      loadOrders();
    } else {
      notify('Failed to update status', 'error');
    }
  } catch (err) {
    notify('Error updating order', 'error');
  }
}

async function deleteOrder(id) {
  if (!confirm('Are you sure you want to delete this order?')) return;
  // Endpoint to destroy order
  notify('Order deletion not fully configured on backend API. Contact administrator.', 'error');
}

// 4. QUOTES
async function loadQuotes() {
  try {
    const res = await fetch('/api/v1/quotes');
    const data = await res.json();
    const rows = document.getElementById('quotes-table-rows');
    rows.innerHTML = '';
    
    if (data.quotes && data.quotes.length > 0) {
      data.quotes.forEach(q => {
        const statusOpts = ['new', 'contacted', 'converted', 'lost'];
        let statusSelect = `<select onchange="updateQuoteStatus('${q.id}', this.value)" class="text-xs border rounded px-2 py-1 outline-none">`;
        statusOpts.forEach(opt => {
          statusSelect += `<option value="${opt}" ${q.status === opt ? 'selected' : ''}>${opt}</option>`;
        });
        statusSelect += `</select>`;
        
        rows.innerHTML += `
          <tr>
            <td class="p-4 font-semibold text-slate-800">${q.name}</td>
            <td class="p-4 text-slate-600">${q.hospital_name || '-'}</td>
            <td class="p-4">
              <div class="text-xs font-semibold text-slate-700">${q.phone}</div>
              <div class="text-xs text-slate-400">${q.email || '-'}</div>
            </td>
            <td class="p-4 text-slate-600 font-semibold">${q.product_name}</td>
            <td class="p-4 text-slate-600">${q.qty || 1}</td>
            <td class="p-4">${statusSelect}</td>
            <td class="p-4 text-right">
              <button onclick="deleteQuote('${q.id}')" class="text-red-500 hover:text-red-700 font-semibold text-xs"><i class="fa-solid fa-trash"></i> Delete</button>
            </td>
          </tr>`;
      });
    } else {
      rows.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-slate-400">No quote requests found.</td></tr>';
    }
  } catch (err) {
    console.error(err);
  }
}

async function updateQuoteStatus(id, newStatus) {
  try {
    const res = await fetch(`/api/v1/quotes/${id}`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ status: newStatus })
    });
    if (res.ok) {
      notify('Quote status updated');
      loadQuotes();
    } else {
      notify('Failed to update status', 'error');
    }
  } catch (err) {
    notify('Error updating quote', 'error');
  }
}

async function deleteQuote(id) {
  if (!confirm('Are you sure you want to delete this quote?')) return;
  try {
    const res = await fetch(`/api/v1/quotes/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    if (res.ok) {
      notify('Quote deleted');
      loadQuotes();
    } else {
      notify('Delete failed', 'error');
    }
  } catch (err) {
    notify('Delete error', 'error');
  }
}

// 5. BLOG
async function loadBlogs() {
  try {
    const res = await fetch('/api/v1/blogs/all'); // Admin all blogs
    const data = await res.json();
    const rows = document.getElementById('blog-table-rows');
    rows.innerHTML = '';
    
    const blogs = Array.isArray(data) ? data : (data.blogs || []);
    
    if (blogs.length > 0) {
      blogs.forEach(b => {
        const image = b.image || '/assets/placeholder-image.jpg';
        const articleDate = b.createdAt ? new Date(b.createdAt).toLocaleDateString() : 'N/A';
        
        rows.innerHTML += `
          <tr>
            <td class="p-4">
              <img src="${image}" alt="${b.title}" class="h-10 w-16 object-cover rounded border border-slate-100">
            </td>
            <td class="p-4 font-semibold text-slate-800">${b.title}</td>
            <td class="p-4 text-slate-500">${b.category || 'General'}</td>
            <td class="p-4 text-slate-500">${b.author || 'Admin'}</td>
            <td class="p-4 text-slate-500">${articleDate}</td>
            <td class="p-4 text-right space-x-2">
              <button onclick="editBlog('${b.id || b._id}')" class="text-blue-500 hover:text-blue-700 font-semibold text-xs"><i class="fa-solid fa-edit"></i> Edit</button>
              <button onclick="deleteBlog('${b.id || b._id}')" class="text-red-500 hover:text-red-700 font-semibold text-xs"><i class="fa-solid fa-trash"></i> Delete</button>
            </td>
          </tr>`;
      });
    } else {
      rows.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-slate-400">No articles found.</td></tr>';
    }
  } catch (err) {
    console.error(err);
  }
}

function openBlogModal() {
  document.getElementById('blog-form').reset();
  document.getElementById('blog-edit-id').value = '';
  document.getElementById('blog-modal-title').textContent = 'Add Article';
  document.getElementById('blog-modal').classList.remove('hidden');
}

function closeBlogModal() {
  document.getElementById('blog-modal').classList.add('hidden');
}

// Blog Submit
document.getElementById('blog-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const id = document.getElementById('blog-edit-id').value;
  const formData = new FormData();
  
  formData.append('title', document.getElementById('blog-title').value);
  formData.append('slug', document.getElementById('blog-slug').value);
  formData.append('category', document.getElementById('blog-category').value);
  formData.append('author', document.getElementById('blog-author').value);
  formData.append('excerpt', document.getElementById('blog-excerpt').value);
  formData.append('content', document.getElementById('blog-content').value);
  
  const imgFile = document.getElementById('blog-image').files[0];
  if (imgFile) {
    formData.append('image', imgFile);
  }

  try {
    let url = '/api/v1/blogs';
    if (id) {
      url = `/api/v1/blogs/${id}`;
      formData.append('_method', 'PUT');
    }
    
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: formData
    });
    
    if (res.ok) {
      notify(id ? 'Article updated' : 'Article created');
      closeBlogModal();
      loadBlogs();
    } else {
      const err = await res.json();
      notify(err.error || 'Failed to save article', 'error');
    }
  } catch (err) {
    notify('Error saving article', 'error');
  }
});

async function editBlog(id) {
  try {
    const res = await fetch(`/api/v1/blogs/${id}`);
    const b = await res.json();
    
    openBlogModal();
    document.getElementById('blog-modal-title').textContent = 'Edit Article';
    document.getElementById('blog-edit-id').value = b.id || b._id;
    document.getElementById('blog-title').value = b.title || '';
    document.getElementById('blog-slug').value = b.slug || '';
    document.getElementById('blog-category').value = b.category || '';
    document.getElementById('blog-author').value = b.author || '';
    document.getElementById('blog-excerpt').value = b.excerpt || '';
    document.getElementById('blog-content').value = b.content || '';
  } catch (err) {
    notify('Error loading article details', 'error');
  }
}

async function deleteBlog(id) {
  if (!confirm('Are you sure you want to delete this article?')) return;
  try {
    const res = await fetch(`/api/v1/blogs/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    if (res.ok) {
      notify('Article deleted');
      loadBlogs();
    } else {
      notify('Delete failed', 'error');
    }
  } catch (err) {
    notify('Delete error', 'error');
  }
}

// 6. SUBSCRIBERS
async function loadSubscribers() {
  try {
    const res = await fetch('/api/v1/notify/subscribers');
    const data = await res.json();
    const rows = document.getElementById('subscribers-table-rows');
    rows.innerHTML = '';
    
    if (data.subscribers && data.subscribers.length > 0) {
      data.subscribers.forEach(s => {
        const signupDate = s.createdAt ? new Date(s.createdAt).toLocaleDateString() : 'N/A';
        rows.innerHTML += `
          <tr>
            <td class="p-4 font-semibold text-slate-800">${s.name || 'N/A'}</td>
            <td class="p-4 text-slate-600">${s.phone}</td>
            <td class="p-4 text-slate-500">${s.source || 'home_newsletter'}</td>
            <td class="p-4 text-slate-500">${signupDate}</td>
            <td class="p-4 text-right">
              <button onclick="deleteSubscriber('${s.id || s._id}')" class="text-red-500 hover:text-red-700 font-semibold text-xs"><i class="fa-solid fa-trash"></i> Delete</button>
            </td>
          </tr>`;
      });
    } else {
      rows.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-slate-400">No subscribers found.</td></tr>';
    }
  } catch (err) {
    console.error(err);
  }
}

async function deleteSubscriber(id) {
  if (!confirm('Are you sure you want to remove this subscriber?')) return;
  try {
    const res = await fetch(`/api/v1/notify/subscribers/${id}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    if (res.ok) {
      notify('Subscriber removed');
      loadSubscribers();
    } else {
      notify('Delete failed', 'error');
    }
  } catch (err) {
    notify('Delete error', 'error');
  }
}

// 7. GOOGLE BUSINESS PROFILE
async function loadGbp() {
  try {
    const res = await fetch('/api/v1/gbp/status');
    const statusData = await res.json();
    
    const statusBox = document.getElementById('gbp-status-box');
    statusBox.innerHTML = '';
    
    if (statusData.connected) {
      statusBox.className = "p-4 rounded-xl border border-green-100 bg-green-50/50 flex flex-col sm:flex-row items-center justify-between gap-4";
      statusBox.innerHTML = `
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-circle-check text-green-500 text-2xl"></i>
          <div>
            <h5 class="font-bold text-green-900">Google Business Connected</h5>
            <p class="text-xs text-green-700 mt-0.5">Locations: ${statusData.locationName || 'N/A'}</p>
          </div>
        </div>
        <button onclick="disconnectGbp()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">Disconnect Google</button>`;
        
      // Load reviews
      const revRes = await fetch('/api/v1/gbp/reviews');
      const revData = await revRes.json();
      const reviewsContainer = document.getElementById('gbp-reviews');
      reviewsContainer.innerHTML = '';
      
      const reviewsList = revData.reviews || [];
      if (reviewsList.length > 0) {
        reviewsList.forEach(r => {
          let starsHtml = '';
          for (let i = 1; i <= 5; i++) {
            starsHtml += `<i class="fa-solid fa-star ${i <= r.starRating ? 'text-amber-400' : 'text-slate-200'} text-xs"></i>`;
          }
          reviewsContainer.innerHTML += `
            <div class="p-3 border rounded-lg bg-slate-50">
              <div class="flex justify-between items-center">
                <span class="font-bold text-slate-800 text-xs">${r.reviewer?.displayName || 'Anonymous'}</span>
                <span>${starsHtml}</span>
              </div>
              <p class="text-xs text-slate-600 mt-1">${r.comment || 'No review comment provided.'}</p>
              <div class="text-[10px] text-slate-400 mt-2">${r.createTime ? new Date(r.createTime).toLocaleDateString() : ''}</div>
            </div>`;
        });
      } else {
        reviewsContainer.innerHTML = '<p class="text-slate-400 text-xs">No Google Reviews synced yet.</p>';
      }
    } else {
      statusBox.className = "p-4 rounded-xl border border-slate-200 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4";
      statusBox.innerHTML = `
        <div class="flex items-center gap-3">
          <i class="fa-solid fa-circle-xmark text-slate-400 text-2xl"></i>
          <div>
            <h5 class="font-bold text-slate-700">Google Business Disconnected</h5>
            <p class="text-xs text-slate-500 mt-0.5">Integrate Google account to manage reviews and sync business info.</p>
          </div>
        </div>
        <button onclick="connectGbp()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1.5">
          <i class="fa-brands fa-google"></i> Connect Account
        </button>`;
      document.getElementById('gbp-reviews').innerHTML = '<p class="text-slate-400 text-xs">Syncing not available. Connect Google account first.</p>';
    }
  } catch (err) {
    console.error(err);
  }
}

function connectGbp() {
  window.location.href = '/api/v1/gbp/connect';
}

async function disconnectGbp() {
  if (!confirm('Disconnect Google Business Profile?')) return;
  try {
    const res = await fetch('/api/v1/gbp/disconnect', {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    });
    if (res.ok) {
      notify('Google Business Profile disconnected');
      loadGbp();
    } else {
      notify('Failed to disconnect', 'error');
    }
  } catch (err) {
    notify('Error disconnecting GBP', 'error');
  }
}

document.getElementById('gbp-post-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  const text = document.getElementById('gbp-post-text').value.trim();
  if (!text) return;
  
  try {
    const res = await fetch('/api/v1/gbp/posts', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify({ summary: text })
    });
    if (res.ok) {
      notify('Announcements posted to Google Business Profile successfully');
      document.getElementById('gbp-post-text').value = '';
    } else {
      notify('Failed to post announcement. Connect GBP or check logs.', 'error');
    }
  } catch (err) {
    notify('Google Post sync error', 'error');
  }
});

// 8. PAGES CMS
async function loadCms(tabName) {
  const pageMap = {
    'cms-homepage': { url: '/api/v1/homepage', formId: 'cms-homepage-form', fieldsId: 'cms-homepage-fields' },
    'cms-services': { url: '/api/v1/services-settings', formId: 'cms-services-form', fieldsId: 'cms-services-fields' },
    'cms-about': { url: '/api/v1/about-settings', formId: 'cms-about-form', fieldsId: 'cms-about-fields' },
    'cms-legal': { url: '/api/v1/legal', formId: 'cms-legal-form', fieldsId: 'cms-legal-fields' }
  };
  
  const config = pageMap[tabName];
  if (!config) return;
  
  try {
    const res = await fetch(config.url);
    const data = await res.json();
    
    // Normalize data shape
    const content = data.data || data;
    
    const fieldsDiv = document.getElementById(config.fieldsId);
    fieldsDiv.innerHTML = '';
    
    // Loop through properties and draw text/textarea fields
    for (const key in content) {
      if (typeof content[key] === 'object' && content[key] !== null) {
        // Nested objects (like contactInfo, heroStats)
        fieldsDiv.innerHTML += `<div class="border-b pb-3 mb-3"><h5 class="text-sm font-bold text-slate-700 capitalize mb-2">${key}</h5>`;
        for (const subKey in content[key]) {
          const val = content[key][subKey] || '';
          fieldsDiv.innerHTML += `
            <div class="mb-3 pl-4">
              <label class="block text-xs font-semibold text-slate-500 uppercase mb-1">${subKey}</label>
              <input type="text" name="${key}[${subKey}]" value="${val}" class="w-full px-3 py-1.5 border border-slate-200 rounded text-sm outline-none focus:ring-2 focus:ring-primary-500">
            </div>`;
        }
        fieldsDiv.innerHTML += `</div>`;
      } else {
        const val = content[key] || '';
        const isLongText = key.toLowerCase().includes('desc') || key.toLowerCase().includes('text') || key.toLowerCase().includes('content') || val.length > 80;
        
        fieldsDiv.innerHTML += `
          <div class="mb-3">
            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">${key}</label>
            ${isLongText 
              ? `<textarea name="${key}" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">${val}</textarea>`
              : `<input type="text" name="${key}" value="${val}" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-primary-500">`
            }
          </div>`;
      }
    }
  } catch (err) {
    console.error('CMS load error', err);
  }
}

// Save CMS
document.querySelectorAll('form[id^="cms-"]').forEach(form => {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formId = form.id;
    
    let url = '/api/v1/homepage';
    if (formId === 'cms-services-form') url = '/api/v1/services-settings';
    else if (formId === 'cms-about-form') url = '/api/v1/about-settings';
    else if (formId === 'cms-legal-form') url = '/api/v1/legal';
    
    // Construct request payload
    const formData = new FormData(form);
    const payload = {};
    
    for (const [key, value] of formData.entries()) {
      // Check if nested field e.g. "contactInfo[phone]"
      const match = key.match(/^([^\[]+)\[([^\]]+)\]$/);
      if (match) {
        const parentKey = match[1];
        const childKey = match[2];
        if (!payload[parentKey]) payload[parentKey] = {};
        payload[parentKey][childKey] = value;
      } else {
        payload[key] = value;
      }
    }
    
    try {
      const res = await fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
      });
      if (res.ok) {
        notify('CMS configuration saved successfully');
      } else {
        notify('Failed to save settings', 'error');
      }
    } catch (err) {
      notify('CMS save error', 'error');
    }
  });
});

// 9. SETTINGS (Profile)
async function loadSettings() {
  try {
    const res = await fetch('/api/v1/auth/me');
    const data = await res.json();
    const u = data.user;
    
    if (u) {
      document.getElementById('settings-name').value = u.name || '';
      document.getElementById('settings-username').value = u.username || '';
      document.getElementById('settings-email').value = u.email || '';
    }
  } catch (err) {
    console.error(err);
  }
}

document.getElementById('profile-form').addEventListener('submit', async (e) => {
  e.preventDefault();
  
  const payload = {
    name: document.getElementById('settings-name').value,
    username: document.getElementById('settings-username').value,
    email: document.getElementById('settings-email').value
  };
  
  const currPass = document.getElementById('settings-current-pass').value;
  const newPass = document.getElementById('settings-new-pass').value;
  
  if (newPass) {
    if (!currPass) {
      notify('Please enter your current password to set a new password', 'error');
      return;
    }
    payload.currentPassword = currPass;
    payload.newPassword = newPass;
  }
  
  try {
    const res = await fetch('/api/v1/auth/profile', {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      },
      body: JSON.stringify(payload)
    });
    
    const data = await res.json();
    if (res.ok) {
      notify('Profile updated successfully');
      document.getElementById('settings-current-pass').value = '';
      document.getElementById('settings-new-pass').value = '';
      // Update displayed email
      document.getElementById('user-email').textContent = data.user?.email || payload.email;
    } else {
      notify(data.error || 'Failed to update profile', 'error');
    }
  } catch (err) {
    notify('Profile update error', 'error');
  }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>
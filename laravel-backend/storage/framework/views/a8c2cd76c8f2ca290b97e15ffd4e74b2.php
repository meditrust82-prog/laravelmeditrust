<?php $__env->startSection('title', 'Sign in to Admin Panel | Meditrust Nepal'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-900 via-primary-800 to-primary-950 py-12 px-4">
  <div class="max-w-md w-full">
    <!-- Logo -->
    <div class="text-center mb-8 flex flex-col items-center">
      <div class="bg-white rounded-2xl p-5 shadow-lg mb-3">
        <a href="/" class="flex items-center">
          <img src="/logo.jpeg" alt="Meditrust Nepal" class="h-20 w-auto object-contain select-none" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <span style="display:none;" class="items-center gap-1.5 font-extrabold text-3xl leading-none">
            <span class="text-cyan-500">MEDI</span><span class="text-emerald-500">TRUST</span>
          </span>
        </a>
      </div>
      <p class="text-primary-300 text-sm mt-1">Admin Panel</p>
    </div>

    <!-- Login Card -->
    <div class="bg-white rounded-2xl shadow-2xl p-8">
      <h2 class="text-xl font-bold text-gray-900 mb-6">Sign in to your account</h2>

      <div id="error-container" class="hidden mb-5 rounded-lg bg-red-50 border border-red-200 p-3">
        <p id="error-message" class="text-sm text-red-700"></p>
      </div>

      <form id="login-form" class="space-y-5">
        <?php echo csrf_field(); ?>
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-1">
            Username or Email
          </label>
          <input
            id="username"
            name="username"
            type="text"
            required
            class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all"
            placeholder="Enter your username or email"
          >
        </div>

        <div>
          <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
            Password
          </label>
          <div class="relative">
            <input
              id="password"
              name="password"
              type="password"
              required
              class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all pr-12"
              placeholder="Enter your password"
            >
            <button
              id="toggle-password"
              type="button"
              class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
            >
              <i id="password-icon" class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>

        <button
          type="submit"
          id="submit-button"
          class="w-full py-3 px-4 rounded-lg bg-primary-600 hover:bg-primary-700 text-white font-medium focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all flex items-center justify-center gap-2"
        >
          <span id="button-spinner" class="hidden w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          <span id="button-text">Sign in</span>
        </button>
      </form>
    </div>

    <p class="text-center text-primary-400 text-sm mt-6">
      &copy; <script>document.write(new Date().getFullYear())</script> Meditrust Nepal. All rights reserved.
    </p>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('login-form');
  const errorContainer = document.getElementById('error-container');
  const errorMessage = document.getElementById('error-message');
  const togglePasswordBtn = document.getElementById('toggle-password');
  const passwordInput = document.getElementById('password');
  const passwordIcon = document.getElementById('password-icon');
  const submitBtn = document.getElementById('submit-button');
  const spinner = document.getElementById('button-spinner');
  const btnText = document.getElementById('button-text');

  // Toggle password visibility
  togglePasswordBtn.addEventListener('click', function() {
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      passwordIcon.classList.remove('fa-eye');
      passwordIcon.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      passwordIcon.classList.remove('fa-eye-slash');
      passwordIcon.classList.add('fa-eye');
    }
  });

  // Handle Login via AJAX
  loginForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Reset errors
    errorContainer.classList.add('hidden');
    errorMessage.textContent = '';
    
    // Set loading state
    submitBtn.disabled = true;
    spinner.classList.remove('hidden');
    btnText.textContent = 'Signing in...';

    const username = document.getElementById('username').value.trim();
    const password = passwordInput.value;

    try {
      const response = await fetch('/api/v1/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          email: username,
          password: password
        })
      });

      const data = await response.json();

      if (response.ok) {
        // Success
        localStorage.setItem('meditrust_was_logged_in', '1');
        
        // Show success and redirect
        btnText.textContent = 'Success! Redirecting...';
        setTimeout(() => {
          window.location.href = '/admin/dashboard';
        }, 800);
      } else {
        // Error
        const status = response.status;
        const msg = data.error || (status === 423 ? 'Account temporarily locked. Try again later.' : 'Invalid credentials');
        
        errorContainer.classList.remove('hidden');
        errorMessage.textContent = msg;
        
        // Reset button
        submitBtn.disabled = false;
        spinner.classList.add('hidden');
        btnText.textContent = 'Sign in';
      }
    } catch (err) {
      console.error('Login error:', err);
      errorContainer.classList.remove('hidden');
      errorMessage.textContent = 'Network error. Please check your connection and try again.';
      
      // Reset button
      submitBtn.disabled = false;
      spinner.classList.add('hidden');
      btnText.textContent = 'Sign in';
    }
  });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/dmk/Desktop/medi-main/laravel-backend/resources/views/admin/login.blade.php ENDPATH**/ ?>
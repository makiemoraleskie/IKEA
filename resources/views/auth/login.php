<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$logoPath = (defined('BASE_URL') ? BASE_URL : '') . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg';
?>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap');
	
	/* Hide scrollbar but allow scrolling */
	html, body {
		scrollbar-width: none; /* Firefox */
		-ms-overflow-style: none; /* IE and Edge */
		color-scheme: light !important;
	}
	
	html::-webkit-scrollbar, body::-webkit-scrollbar {
		display: none; /* Chrome, Safari, Opera */
		width: 0;
		height: 0;
	}
	
	/* Force light mode - prevent dark mode */
	html[data-theme="dark"],
	body[data-theme="dark"],
	html.dark,
	body.dark,
	html[data-theme="dark"] *,
	body[data-theme="dark"] * {
		color-scheme: light !important;
		background-color: transparent !important;
	}
	
	/* Ensure all elements stay in light mode */
	* {
		color-scheme: light !important;
	}
	
	.login-container {
		background: linear-gradient(135deg, #FAD1E8 0%, #CDEFD8 100%);
		overflow: hidden;
	}
	
	.login-container section::-webkit-scrollbar {
		display: none;
	}
	
	.login-container section {
		scrollbar-width: none;
		-ms-overflow-style: none;
	}
	
	.left-panel-gradient {
		background: linear-gradient(180deg, #FAD1E8 0%, #CDEFD8 100%);
	}
	
	.green-gradient-button {
		background: linear-gradient(135deg, #00A451 0%, #008E3B 100%);
		box-shadow: 0 4px 12px rgba(0, 142, 59, 0.25);
	}
	
	.green-gradient-button:hover {
		background: linear-gradient(135deg, #008E3B 0%, #007A32 100%);
		box-shadow: 0 6px 16px rgba(0, 142, 59, 0.35);
	}
	
	.script-headline {
		font-family: 'Playfair Display', serif;
		font-style: italic;
		font-size: 38px;
		line-height: 1.2;
		color: #382E2E;
	}
	
	.body-text {
		color: #4A4A4A;
		font-size: 17px;
		line-height: 1.7;
	}
	
	.location-badge {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 6px 16px;
		background: rgba(255, 255, 255, 0.4);
		border-radius: 9999px;
		font-size: 14px;
		font-weight: 500;
		color: #382E2E;
	}
	
	.green-dot {
		width: 8px;
		height: 8px;
		background: #00A451;
		border-radius: 50%;
	}
	
	/* Modal Styles - Modern Design */
	.modal-overlay {
		position: fixed;
		inset: 0;
		background: linear-gradient(135deg, rgba(250, 209, 232, 0.4) 0%, rgba(205, 239, 216, 0.4) 100%);
		backdrop-filter: blur(12px) saturate(180%);
		-webkit-backdrop-filter: blur(12px) saturate(180%);
		z-index: 9999;
		display: flex;
		align-items: center;
		justify-content: center;
		opacity: 0;
		visibility: hidden;
		transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.4s;
		padding: 20px;
	}
	
	.modal-overlay.show {
		opacity: 1;
		visibility: visible;
	}
	
	.modal-content {
		background: white;
		border-radius: 32px;
		padding: 40px;
		max-width: 420px;
		width: 100%;
		box-shadow: 
			0 25px 50px -12px rgba(0, 0, 0, 0.25),
			0 0 0 1px rgba(0, 0, 0, 0.05);
		transform: scale(0.85) translateY(20px);
		opacity: 0;
		transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.4s ease;
		position: relative;
		overflow: hidden;
	}
	
	.modal-content::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		right: 0;
		height: 4px;
		background: linear-gradient(90deg, #FAD1E8 0%, #CDEFD8 100%);
	}
	
	.modal-overlay.show .modal-content {
		transform: scale(1) translateY(0);
		opacity: 1;
	}
	
	.modal-icon {
		width: 64px;
		height: 64px;
		border-radius: 20px;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 24px;
		box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
		transition: transform 0.3s ease;
	}
	
	.modal-overlay.show .modal-icon {
		animation: iconBounce 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) 0.2s;
	}
	
	@keyframes iconBounce {
		0% { transform: scale(0); }
		50% { transform: scale(1.1); }
		100% { transform: scale(1); }
	}
	
	.modal-icon.error {
		background: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%);
		color: #DC2626;
	}
	
	.modal-icon.info {
		background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
		color: #2563EB;
	}
	
	.modal-title {
		font-size: 24px;
		font-weight: 700;
		letter-spacing: -0.5px;
		margin-bottom: 12px;
	}
	
	.modal-message {
		font-size: 15px;
		line-height: 1.6;
		color: #6B7280;
	}
	
	.modal-button {
		margin-top: 28px;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}
	
	.modal-button:hover {
		transform: translateY(-2px);
		box-shadow: 0 8px 20px rgba(0, 142, 59, 0.35);
	}
	
	.modal-button:active {
		transform: translateY(0);
	}
</style>

<div class="h-screen login-container flex items-center justify-center px-4 py-4">
	<div class="w-full max-w-6xl h-full max-h-[95vh] bg-white rounded-[32px] shadow-2xl overflow-hidden flex" style="box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);">
		<div class="grid lg:grid-cols-2 w-full h-full">
			<!-- Left Panel - Brand Story Section -->
			<section class="hidden lg:flex flex-col justify-between items-center left-panel-gradient p-12" style="padding: 48px 60px;">
				<!-- Logo Area - Top -->
				<div class="flex flex-col items-center space-y-4">
					<!-- Square Logo -->
					<div class="w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center" style="box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);">
						<img src="<?php echo htmlspecialchars($logoPath); ?>" alt="Logo" class="w-14 h-14 object-cover rounded-xl">
					</div>
					
					<!-- Location Badge -->
					<div class="location-badge">
						<span class="green-dot"></span>
						<span class="uppercase tracking-wide font-medium">ORMOC CITY, PH</span>
					</div>
				</div>
				
				<!-- Middle Content -->
				<div class="w-full max-w-md space-y-12 text-center flex-1 flex flex-col justify-start pt-16">
					<!-- Headline -->
					<h1 class="script-headline" style="font-size: 34px; margin-top: -20px;">
						Well-loved pastry and snack shop since 1990.
					</h1>
					
					<!-- Description Paragraph -->
					<p class="body-text" style="font-size: 16px; line-height: 2;">
						A snack shop in Ormoc City, Philippines. Opened in 1990 it became popular because of its chocolate and mango cake, and their specialties like Palabok, Siopao, Mami, Empanada, Arroz Caldo to name a few.
					</p>
				</div>
				
				<!-- Footer - Bottom -->
				<div class="w-full border-t border-gray-300/40 pt-4">
					<div class="flex items-center justify-center gap-2 text-sm text-gray-500">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						<span>Serving smiles since 1990</span>
					</div>
				</div>
			</section>

			<!-- Right Panel - Login Form Section -->
			<section class="p-8 bg-white flex flex-col justify-between overflow-y-auto" style="padding: 40px 48px;">
				<!-- Main Content -->	
				<div class="max-w-md mx-auto w-full space-y-16 flex-1">
					<!-- Logo & Heading -->
					<div class="flex flex-col items-center space-y-2">
						<!-- Super-Title -->
						<p class="text-[11px] uppercase tracking-[0.15em] text-gray-500 font-semibold" style="letter-spacing: 0.15em;">DASHBOARD PORTAL</p>
						
						<!-- Main Title -->
						<h2 class="text-[28px] font-bold text-gray-900" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">Welcome</h2>
						
						<!-- Subtitle -->
						<p class="text-sm text-gray-600" style="font-family: 'Inter', sans-serif;">Sign in to access your account</p>
					</div>

					<?php
					$errorMessage = !empty($error) ? htmlspecialchars($error) : '';
					$status = $_GET['status'] ?? '';
					$statusMessages = [
						'password-updated' => 'Password updated. Please sign in with your new credentials.',
						'disabled' => 'Your account has been disabled. Contact an administrator.',
						'expired' => 'Your session expired. Please sign in again.',
					];
					$statusMessage = ($status && isset($statusMessages[$status])) ? htmlspecialchars($statusMessages[$status]) : '';
					?>

					<!-- Login Form -->
					<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/login" class="space-y-5 mt-10" novalidate>
						<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

						<!-- Email Field -->
						<div class="space-y-4">
							<label for="email" class="block text-sm font-medium text-gray-700" style="font-family: 'Inter', sans-serif;">Email Address</label>
							<input
								id="email"
								name="email"
								type="email"
								placeholder="Enter your email address"
								class="w-full rounded-2xl border px-4 py-3 text-base text-gray-900 placeholder-gray-400 transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
								style="border-color: #E6E6E6; font-size: 16px; font-family: 'Inter', sans-serif;"
								required
							/>
						</div>

						<!-- Password Field -->
						<div class="space-y-2">
							<label for="password" class="block text-sm font-medium text-gray-700" style="font-family: 'Inter', sans-serif;">Password</label>
							<div class="relative">
								<input
									id="password"
									name="password"
									type="password"
									placeholder="Enter your password"
									class="w-full rounded-2xl border px-4 py-3 pr-12 text-base text-gray-900 placeholder-gray-400 transition focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/20"
									style="border-color: #E6E6E6; font-size: 16px; font-family: 'Inter', sans-serif;"
									required
								/>
								<button
									type="button"
									id="togglePassword"
									class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none transition"
									aria-label="Toggle password visibility"
								>
									<!-- Eye icon - shown when password is hidden -->
									<svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
									</svg>
									<!-- Eye-off icon - shown when password is visible -->
									<svg id="eyeOffIcon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.29 3.29m0 0L3 3m3.29 3.29L3 3"></path>
									</svg>
								</button>
							</div>
						</div>
						<br>

						<!-- Continue Button -->
						<button
							type="submit"
							id="loginButton"
							class="w-full green-gradient-button rounded-full py-4 px-6 text-base font-semibold text-white transition-all duration-200 flex items-center justify-center gap-2 mt-8"
							style="font-family: 'Inter', sans-serif; font-size: 16px;"
						>
							<svg id="loginSpinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							<span id="loginButtonText">Login</span>
						</button>
					</form>
				</div>

				<!-- Support Section - Footer -->
				<div class="max-w-md mx-auto w-full pt-6">
					<!-- Divider -->
					<div class="relative flex items-center mb-4">
						<div class="flex-grow border-t" style="border-color: #E6E6E6;"></div>
						<span class="flex-shrink mx-4 text-[10px] font-semibold uppercase tracking-[0.2em] text-gray-500" style="font-family: 'Inter', sans-serif;">SUPPORT</span>
						<div class="flex-grow border-t" style="border-color: #E6E6E6;"></div>
					</div>
					
					<!-- Support Content -->
					<div class="text-center space-y-2">
						<p class="text-sm font-semibold" style="color: #00A451; font-family: 'Inter', sans-serif;">FORGOT CREDENTIALS?</p>
						<p class="text-sm text-gray-600" style="font-family: 'Inter', sans-serif; line-height: 1.6;">
							Check in with the Owner for access.
						</p>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>
<style>
	/* Import Inter font */
	@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');

	/* Apply Inter as base font family globally */
	* {
		font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}

	@keyframes fade-in {
		from {
			opacity: 0;
			transform: translateY(30px) scale(0.96);
		}
		to {
			opacity: 1;
			transform: translateY(0) scale(1);
		}
	}

	@keyframes shake {
		0%, 100% { transform: translateX(0); }
		10%, 30%, 50%, 70%, 90% { transform: translateX(-4px); }
		20%, 40%, 60%, 80% { transform: translateX(4px); }
	}

	@keyframes float {
		0%, 100% {
			transform: translate(0, 0) scale(1);
			opacity: 0.6;
		}
		50% {
			transform: translate(30px, -30px) scale(1.15);
			opacity: 0.8;
		}
	}

	@keyframes float-delayed {
		0%, 100% {
			transform: translate(0, 0) scale(1);
			opacity: 0.5;
		}
		50% {
			transform: translate(-30px, 30px) scale(1.1);
			opacity: 0.7;
		}
	}

	.animate-fade-in {
		animation: fade-in 0.7s cubic-bezier(0.16, 1, 0.3, 1);
	}

	.animate-shake {
		animation: shake 0.5s ease-in-out;
	}

	.animate-float {
		animation: float 25s ease-in-out infinite;
	}

	.animate-float-delayed {
		animation: float-delayed 30s ease-in-out infinite;
	}

	/* Enhanced logo clarity and sharpness */
	.logo-wrapper {
		position: relative;
		display: inline-block;
	}

	.logo-image {
		image-rendering: -webkit-optimize-contrast;
		image-rendering: crisp-edges;
		image-rendering: auto;
		object-fit: contain;
		object-position: center;
		-webkit-backface-visibility: hidden;
		backface-visibility: hidden;
		-webkit-transform: translateZ(0);
		transform: translateZ(0);
		filter: contrast(1.1) brightness(1.05) saturate(1.1);
		will-change: transform;
	}

	/* Ensure sharp rendering on high DPI displays */
	@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
		.logo-image {
			image-rendering: -webkit-optimize-contrast;
			image-rendering: crisp-edges;
		}
	}

	/* Improve logo clarity on hover */
	.logo-image:hover {
		filter: contrast(1.15) brightness(1.1) saturate(1.15);
	}

	/* Enhanced input focus effects - removed to prevent flickering */
	/* input:focus {
		transform: translateY(-1px);
	} */

	/* Button shimmer effect on hover */
	button[type="submit"] {
		position: relative;
		overflow: hidden;
	}

	button[type="submit"]::before {
		content: '';
		position: absolute;
		top: 0;
		left: -100%;
		width: 100%;
		height: 100%;
		background: linear-gradient(
			90deg,
			transparent,
			rgba(255, 255, 255, 0.3),
			transparent
		);
		transition: left 0.5s;
		pointer-events: none;
	}

	button[type="submit"]:hover::before {
		left: 100%;
	}

	/* Smooth transitions for interactive elements */
	button, input, label {
		transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter;
		transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
		transition-duration: 200ms;
	}

	/* Glassmorphism enhancement */
	.backdrop-blur-xl {
		backdrop-filter: blur(20px);
		-webkit-backdrop-filter: blur(20px);
	}

	/* Enhanced shadow system */
	.shadow-3xl {
		box-shadow: 0 35px 60px -15px rgba(0, 0, 0, 0.3);
	}

	/* Border width utilities */
	.border-3 {
		border-width: 3px;
	}

	/* IKEA-style heading - bold, modern, distinctive */
	.ikea-heading {
		font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
		font-weight: 900;
		letter-spacing: -0.5px;
		text-transform: uppercase;
		line-height: 1.1;
	}
</style>

<script>
	function togglePassword() {
		const passwordInput = document.getElementById('password');
		const eyeIcon = document.getElementById('eyeIcon');
		const eyeOffIcon = document.getElementById('eyeOffIcon');
		
		if (passwordInput.type === 'password') {
			passwordInput.type = 'text';
			eyeIcon.classList.remove('hidden');
			eyeOffIcon.classList.add('hidden');
		} else {
			passwordInput.type = 'password';
			eyeIcon.classList.add('hidden');
			eyeOffIcon.classList.remove('hidden');
		}
	}

	// Add loading state to form submission
	document.getElementById('loginForm')?.addEventListener('submit', function(e) {
		const submitButton = this.querySelector('button[type="submit"]');
		if (submitButton && !submitButton.disabled) {
			submitButton.disabled = true;
			submitButton.innerHTML = '<span class="relative z-10 flex items-center justify-center gap-2"><svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Signing in...</span>';
		}
	});
</script>

<!-- Error/Status Modal -->
<div id="errorModal" class="modal-overlay">
	<div class="modal-content">
		<div id="modalIcon" class="modal-icon error">
			<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
				<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path>
			</svg>
		</div>
		<h3 id="modalTitle" class="modal-title text-gray-900 text-center" style="font-family: 'Inter', sans-serif;">Error</h3>
		<p id="modalMessage" class="modal-message text-center" style="font-family: 'Inter', sans-serif;"></p>
		<button id="modalCloseBtn" class="modal-button w-full green-gradient-button rounded-full py-3.5 px-6 text-base font-semibold text-white" style="font-family: 'Inter', sans-serif;">
			Got it
		</button>
	</div>
</div>

<script>
// Force light mode - prevent dark mode
(function() {
	let isUpdating = false;
	
	// Set light mode immediately
	function setLightMode() {
		if (isUpdating) return;
		isUpdating = true;
		
		if (document.documentElement.getAttribute('data-theme') !== 'light') {
			document.documentElement.setAttribute('data-theme', 'light');
		}
		if (document.body.getAttribute('data-theme') !== 'light') {
			document.body.setAttribute('data-theme', 'light');
		}
		document.documentElement.classList.remove('dark');
		document.body.classList.remove('dark');
		
		setTimeout(() => { isUpdating = false; }, 50);
	}
	
	// Set initial light mode
	setLightMode();
	
	// Prevent any dark mode changes with debouncing
	let observerTimeout;
	const observer = new MutationObserver(function(mutations) {
		clearTimeout(observerTimeout);
		observerTimeout = setTimeout(function() {
			let needsUpdate = false;
			mutations.forEach(function(mutation) {
				if (mutation.type === 'attributes') {
					if (mutation.attributeName === 'data-theme') {
						const newValue = mutation.target.getAttribute('data-theme');
						if (newValue && newValue !== 'light') {
							needsUpdate = true;
						}
					}
					if (mutation.attributeName === 'class') {
						if (mutation.target.classList.contains('dark')) {
							needsUpdate = true;
						}
					}
				}
			});
			if (needsUpdate) {
				setLightMode();
			}
		}, 10);
	});
	
	observer.observe(document.documentElement, {
		attributes: true,
		attributeFilter: ['data-theme', 'class']
	});
	observer.observe(document.body, {
		attributes: true,
		attributeFilter: ['data-theme', 'class']
	});
	
	// Watch for form submissions
	document.addEventListener('submit', function(e) {
		setTimeout(setLightMode, 100);
	});
})();

document.addEventListener('DOMContentLoaded', function() {
	// Ensure light mode is maintained
	if (document.documentElement.getAttribute('data-theme') !== 'light') {
		document.documentElement.setAttribute('data-theme', 'light');
	}
	if (document.body.getAttribute('data-theme') !== 'light') {
		document.body.setAttribute('data-theme', 'light');
	}
	document.documentElement.classList.remove('dark');
	document.body.classList.remove('dark');
	
	const togglePassword = document.getElementById('togglePassword');
	const passwordInput = document.getElementById('password');
	const eyeIcon = document.getElementById('eyeIcon');
	const eyeOffIcon = document.getElementById('eyeOffIcon');
	
	if (togglePassword && passwordInput) {
		togglePassword.addEventListener('click', function() {
			const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
			passwordInput.setAttribute('type', type);
			
			if (type === 'text') {
				eyeIcon.classList.add('hidden');
				eyeOffIcon.classList.remove('hidden');
			} else {
				eyeIcon.classList.remove('hidden');
				eyeOffIcon.classList.add('hidden');
			}
		});
	}
	
	// Modal functionality
	const errorModal = document.getElementById('errorModal');
	const modalMessage = document.getElementById('modalMessage');
	const modalTitle = document.getElementById('modalTitle');
	const modalIcon = document.getElementById('modalIcon');
	const modalCloseBtn = document.getElementById('modalCloseBtn');
	
	function showModal(message, type = 'error') {
		if (!errorModal || !modalMessage) return;
		
		modalMessage.textContent = message;
		if (type === 'error') {
			modalTitle.textContent = 'Error';
			modalIcon.className = 'modal-icon error';
			modalIcon.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"></path></svg>';
		} else {
			modalTitle.textContent = 'Notice';
			modalIcon.className = 'modal-icon info';
			modalIcon.innerHTML = '<svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"></path></svg>';
		}
		errorModal.classList.add('show');
	}
	
	function hideModal() {
		if (errorModal) {
			errorModal.classList.remove('show');
		}
	}
	
	// Close modal on button click
	if (modalCloseBtn) {
		modalCloseBtn.addEventListener('click', hideModal);
	}
	
	// Close modal on overlay click
	if (errorModal) {
		errorModal.addEventListener('click', function(e) {
			if (e.target === errorModal) {
				hideModal();
			}
		});
	}
	
	// Show modal if there's an error or status message - show immediately
	<?php if (!empty($errorMessage)): ?>
		setTimeout(() => showModal(<?php echo json_encode($errorMessage); ?>, 'error'), 100);
	<?php elseif (!empty($statusMessage)): ?>
		setTimeout(() => showModal(<?php echo json_encode($statusMessage); ?>, 'info'), 100);
	<?php endif; ?>
	
	// Login button loading state
	const loginForm = document.querySelector('form[method="post"][action*="/login"]');
	const loginButton = document.getElementById('loginButton');
	const loginSpinner = document.getElementById('loginSpinner');
	const loginButtonText = document.getElementById('loginButtonText');
	
	if (loginForm && loginButton && loginSpinner && loginButtonText) {
		loginForm.addEventListener('submit', function(e) {
			// Show loading spinner
			loginSpinner.classList.remove('hidden');
			loginButton.disabled = true;
			loginButton.style.opacity = '0.8';
			loginButton.style.cursor = 'not-allowed';
		});
	}
});
</script>

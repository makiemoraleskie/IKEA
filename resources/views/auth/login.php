<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$logoPath = (defined('BASE_URL') ? BASE_URL : '') . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg';
?>

<div class="h-screen bg-gradient-to-br from-[#FEF7F0] via-[#FFF5F9] to-[#F0F9F4] flex items-center justify-center px-4 py-6 sm:px-6 sm:py-8 relative overflow-hidden" style="font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
	<!-- Enhanced animated background elements -->
	<div class="absolute inset-0 overflow-hidden pointer-events-none">
		<div class="absolute -top-40 -right-40 w-96 h-96 bg-gradient-to-br from-[#FCBBE9]/30 to-[#FFB6E1]/20 rounded-full blur-3xl animate-float"></div>
		<div class="absolute -bottom-40 -left-40 w-96 h-96 bg-gradient-to-tr from-[#A8E6CF]/25 to-[#88D8A3]/15 rounded-full blur-3xl animate-float-delayed"></div>
		<div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-r from-[#FCBBE9]/10 via-transparent to-[#A8E6CF]/10 rounded-full blur-3xl"></div>
	</div>

	<div class="w-full max-w-6xl bg-white/80 backdrop-blur-xl border border-white/60 rounded-2xl shadow-xl shadow-gray-200/20 overflow-hidden animate-fade-in relative z-10">
		<div class="grid gap-0 lg:grid-cols-2 h-full">
			<!-- Left Brand Section - Enhanced -->
			<section class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-[#FCBBE9] via-[#F5E6F0] to-[#A8E6CF] text-[#1d1b1e] p-8 lg:p-10 backdrop-blur-sm overflow-hidden">
				<!-- Decorative glassmorphism elements -->
				<div class="absolute top-0 right-0 w-64 h-64 bg-white/20 rounded-full -mr-32 -mt-32 blur-2xl"></div>
				<div class="absolute bottom-0 left-0 w-48 h-48 bg-white/15 rounded-full -ml-24 -mb-24 blur-xl"></div>
				
				<div class="space-y-6 relative z-10">
					<div class="flex flex-col items-center gap-4">
						<div class="relative logo-wrapper">
							<img 
								src="<?php echo htmlspecialchars($logoPath); ?>" 
								alt="IKEA Commissary logo" 
								class="logo-image h-20 w-20 rounded-2xl border-3 border-white/90 bg-white/70 p-2.5 shadow-xl shadow-[#008000]/20 transition-all duration-500 hover:scale-110 hover:rotate-3 hover:shadow-2xl"
								loading="eager"
								fetchpriority="high"
							>
							<div class="absolute inset-0 rounded-2xl bg-gradient-to-br from-white/40 to-transparent pointer-events-none"></div>
						</div>
						<div class="inline-flex items-center gap-2.5 px-3 py-1.5 rounded-full bg-white/50 backdrop-blur-md border border-white/70 shadow-md">
							<span class="h-2 w-2 rounded-full bg-[#008000] animate-pulse shadow-md shadow-[#008000]/50"></span>
							<span class="text-xs uppercase tracking-[0.3em] text-[#1b1720] font-semibold">Ormoc City, PH</span>
						</div>
					</div>
					<br>
					
					<div class="space-y-4 max-w-md mx-auto">
						<h1 class="text-3xl lg:text-4xl text-black text-center mb-4 font-bold" style="font-family: 'Dancing Script', cursive;">
							Well-loved pastry and snack shop since 1990.
						</h1>
						<br>
						<p class="text-sm lg:text-base text-[#423a44] leading-relaxed font-normal text-justify mt-6">
							A snack shop in Ormoc City, Philippines. Opened in 1990 it became popular because of its chocolate and mango cake, and their specialties like Palabok, Siopao, Mami, Empanada, Arroz Caldo to name a few.
						</p>
					</div>
				</div>

				<!-- Subtle decorative bottom element -->
				<div class="relative z-10 mt-4 pt-4 border-t border-white/30">
					<div class="flex items-center justify-center gap-2 text-xs text-[#5f5b60]/80">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
						</svg>
						<span class="font-medium">Serving smiles since 1990</span>
					</div>
				</div>
			</section>

			<!-- Right Login Form Section - Enhanced -->
			<section class="p-6 sm:p-8 lg:p-10 bg-white/95 backdrop-blur-sm flex flex-col justify-center">
				<!-- Header -->
				<div class="mb-6 sm:mb-8 text-center">
					<div class="flex items-center justify-center gap-3 mb-4">
						<div class="relative logo-wrapper">
							<img 
								src="<?php echo htmlspecialchars($logoPath); ?>" 
								alt="IKEA Commissary logo" 
								class="logo-image w-14 h-14 rounded-xl border-2 border-gray-200/40 bg-gradient-to-br from-[#FEF7F0] to-[#FFF5F9] shadow-md transition-all duration-300 hover:scale-110 hover:shadow-lg"
								loading="eager"
								fetchpriority="high"
							>
						</div>
					</div>
					<div>
						<p class="text-xs uppercase tracking-[0.3em] text-gray-500 font-bold mb-1.5">Dashboard Portal</p>
						<h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight mb-1.5">Welcome</h2>
						<p class="text-gray-600 text-sm font-normal">Sign in to access your account</p>
					</div>
				</div>

				<?php if (!empty($error)): ?>
					<div class="mb-5 rounded-xl border-2 border-red-200/90 bg-gradient-to-r from-red-50/95 to-red-50/60 backdrop-blur-sm px-4 py-3 text-xs sm:text-sm text-red-800 shadow-md shadow-red-100/50 animate-shake">
						<div class="flex items-start gap-2.5">
							<svg class="w-4 h-4 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
							<span class="font-semibold"><?php echo htmlspecialchars($error); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/login" class="space-y-4 sm:space-y-5" novalidate id="loginForm">
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

					<div class="space-y-2">
						<label for="email" class="block text-xs sm:text-sm font-semibold text-gray-700 tracking-wide">Email Address</label>
						<div class="relative">
							<input
								id="email"
								name="email"
								type="email"
								placeholder="Enter your email address"
								autocomplete="email"
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-4 py-3 text-sm sm:text-base text-gray-900 placeholder-gray-400 font-normal transition-all duration-300 hover:border-gray-400 hover:bg-white hover:shadow-md focus:border-[#008000] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#008000]/15 focus:shadow-lg"
								required
							/>
						</div>
					</div>

					<div class="space-y-2">
						<label for="password" class="block text-xs sm:text-sm font-semibold text-gray-700 tracking-wide">Password</label>
						<div class="relative">
							<input
								id="password"
								name="password"
								type="password"
								placeholder="Enter your password"
								autocomplete="current-password"
								class="w-full rounded-xl border-2 border-gray-200/80 bg-gradient-to-br from-gray-50/80 to-white px-4 pr-12 py-3 text-sm sm:text-base text-gray-900 placeholder-gray-400 font-normal transition-all duration-300 hover:border-gray-400 hover:bg-white hover:shadow-md focus:border-[#008000] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#008000]/15 focus:shadow-lg"
								required
							/>
							<button
								type="button"
								onclick="togglePassword()"
								class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#008000] focus:outline-none transition-colors duration-200 p-1 rounded-lg hover:bg-gray-100"
								aria-label="Toggle password visibility"
							>
								<svg id="eyeIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
								</svg>
								<svg id="eyeOffIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
								</svg>
							</button>
						</div>
					</div>
					<br>

					<button
						type="submit"
						class="w-full rounded-xl bg-gradient-to-b from-[#00A86B] to-[#008000] py-3.5 sm:py-4 text-sm sm:text-base font-bold text-white shadow-md hover:opacity-90 hover:shadow-lg transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-[#008000]/30 focus:ring-offset-2 focus:ring-offset-white relative overflow-hidden"
					>
						<span class="flex items-center justify-center gap-2">
							<span>Continue</span>
							<svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
							</svg>
						</span>
					</button>
				</form>


				<!-- Footer Section -->
				<div class="mt-4 sm:mt-5 pt-3 sm:pt-4 border-t border-gray-200/60 text-center">
				
					<div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-gray-400 mb-3">
						<span class="block h-px flex-1 bg-gradient-to-r from-transparent via-gray-300 to-gray-300"></span>
						Support
						<span class="block h-px flex-1 bg-gradient-to-l from-transparent via-gray-300 to-gray-300"></span>
					</div>

					<div class="px-3 py-2 text-xs text-gray-700 mx-auto max-w-xs">
						<p class="font-bold uppercase tracking-[0.1em] text-[10px] text-[#008000] mb-1">Lost Credentials?</p>
						<p class="leading-snug font-normal text-[11px]">Check in with the Owner for access. We don't pin passwords to the wall anymore.</p>
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

	// Removed input animation to prevent flickering conflicts with CSS transforms
</script>

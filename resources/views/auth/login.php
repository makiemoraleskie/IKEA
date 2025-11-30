<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$logoPath = (defined('BASE_URL') ? BASE_URL : '') . '/resources/views/logo/540473678_1357706066360607_6728109697986200356_n (1).jpg';
$chant = [
	'Sift the flour, fold the dreams, taste the city’s slow heartbeat.',
	'Keep the ovens glowing; Ormoc wakes up to stories and sugar.',
	'Cakes remember every celebration—you just have to listen.',
];
?>

<div class="min-h-screen bg-gradient-to-br from-[#F5E6F0] via-white to-[#FCBBE9] flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
	<div class="w-full max-w-5xl bg-white border border-[#F5E6F0] rounded-[28px] shadow-2xl overflow-hidden">
		<div class="grid gap-0 lg:grid-cols-2">
			<section class="relative hidden lg:flex flex-col justify-between bg-gradient-to-br from-[#FCBBE9] via-[#F5E6F0] to-[#008000] text-[#1d1b1e] p-10">
				<div class="space-y-5">
					<img src="<?php echo htmlspecialchars($logoPath); ?>" alt="IKEA Commissary logo" class="h-16 w-16 rounded-2xl border border-white/70 bg-white/40 p-2 shadow-lg shadow-[#008000]/15">
					<div class="inline-flex items-center gap-3 text-xs uppercase tracking-[0.35em] text-[#5f5b60]">
						<span class="h-2 w-2 rounded-full bg-white/80"></span>
						Ormoc City, PH
					</div>
					<h1 class="text-3xl font-semibold leading-tight text-[#1b1720]">
						Well-loved pastry and snack shop since 1990.
					</h1>
					<p class="text-[#423a44] text-sm max-w-md leading-relaxed">
						A well-loved pastry and snack shop in Ormoc City, Philippines. Opened in 1990 it became popular because of its chocolate and mango cake, and their specialties like Palabok, Siopao, Mami, Empanada, Arroz Caldo to name a few.
					</p>
				</div>
				<div class="rounded-2xl bg-white/40 border border-white/60 p-5 space-y-4 text-sm text-[#3c383d]">
					<p class="font-semibold text-[#1c3a2a] uppercase tracking-[0.25em] text-xs">Midnight kitchen mantra</p>
					<?php foreach ($chant as $line): ?>
						<p class="italic text-[#2a232c]"><?php echo htmlspecialchars($line); ?></p>
					<?php endforeach; ?>
					<div class="text-xs text-[#4c4350] pt-2 border-t border-white/60">“Don’t ask for passwords. Ask for purpose.”</div>
				</div>
			</section>

			<section class="p-8 sm:p-10 bg-white">
				<div class="flex items-center gap-3 mb-8">
					<img src="<?php echo htmlspecialchars($logoPath); ?>" alt="IKEA Commissary logo" class="w-12 h-12 rounded-2xl border border-[#FCBBE9] bg-[#F5E6F0] object-cover">
					<div>
						<p class="text-xs uppercase tracking-[0.3em] text-gray-400">Portal</p>
						<p class="text-2xl font-semibold text-gray-900">Dashboard Sign-in</p>
					</div>
				</div>

				<?php if (!empty($error)): ?>
					<div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
						<?php echo htmlspecialchars($error); ?>
					</div>
				<?php endif; ?>
				<?php
				$status = $_GET['status'] ?? '';
				$statusMessages = [
					'password-updated' => 'Password updated. Please sign in with your new credentials.',
					'disabled' => 'Your account has been disabled. Contact an administrator.',
					'expired' => 'Your session expired. Please sign in again.',
				];
				if ($status && isset($statusMessages[$status])): ?>
					<div class="mb-5 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700">
						<?php echo htmlspecialchars($statusMessages[$status]); ?>
					</div>
				<?php endif; ?>

				<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/login" class="space-y-5" novalidate>
					<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">

					<div class="space-y-2">
						<label for="email" class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email</label>
						<input
							id="email"
							name="email"
							type="email"
							placeholder="name@company.com"
							class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-[#008000] focus:outline-none focus:ring-2 focus:ring-[#008000]/20"
							required
						/>
					</div>

					<div class="space-y-2">
						<label for="password" class="text-xs font-semibold uppercase tracking-wide text-gray-500">Password</label>
						<input
							id="password"
							name="password"
							type="password"
							placeholder="••••••••"
							class="w-full rounded-2xl border border-gray-200 px-4 py-3 text-gray-900 placeholder-gray-400 transition focus:border-[#008000] focus:outline-none focus:ring-2 focus:ring-[#008000]/20"
							required
						/>
					</div>

					<button
						type="submit"
						class="w-full rounded-2xl bg-[#008000] py-3 text-base font-semibold text-white shadow-lg shadow-[#008000]/25 transition hover:bg-[#006a00] focus:outline-none focus:ring-2 focus:ring-[#008000]/40 focus:ring-offset-2 focus:ring-offset-white"
					>
						Continue
					</button>
				</form>

				<div class="mt-10 pt-8 border-t border-gray-100">
					<div class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.35em] text-gray-400 mb-4">
						<span class="block h-px flex-1 bg-gray-200"></span>
						Studio Notes
						<span class="block h-px flex-1 bg-gray-200"></span>
					</div>

					<div class="grid gap-4">
						<div class="rounded-2xl border border-[#F5E6F0] bg-[#FCBBE9]/30 px-4 py-3 text-sm text-[#2d2730]">
							<p class="font-semibold uppercase tracking-[0.2em] text-xs text-[#008000]">Quiet Policy</p>
							<p>The login room stays silent so the ovens can hum. Kindly keep your secrets to yourself.</p>
						</div>
						<div class="rounded-2xl border border-[#F5E6F0] bg-white px-4 py-3 shadow-sm text-sm text-[#2d2730]">
							<p class="font-semibold uppercase tracking-[0.2em] text-xs text-[#008000]">Lost Credentials?</p>
							<p>Check in with the Owner for access. We don’t pin passwords to the wall anymore.</p>
						</div>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

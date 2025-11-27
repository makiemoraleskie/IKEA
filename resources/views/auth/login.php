<?php
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
$logoPath = (defined('BASE_URL') ? BASE_URL : '') . '/public/uploads/ikea-commissary-logo.jpg';
$demoAccounts = [
	['role' => 'Owner', 'email' => 'makiemorales2@gmail.com', 'note' => 'Executive Oversight'],
	['role' => 'Manager', 'email' => 'manager@demo.local', 'note' => 'Approvals & Scheduling'],
	['role' => 'Stock Handler', 'email' => 'stock@demo.local', 'note' => 'Warehouse Ops'],
	['role' => 'Purchaser', 'email' => 'purchaser@demo.local', 'note' => 'Supplier Coordination'],
	['role' => 'Kitchen Staff', 'email' => 'kitchen@demo.local', 'note' => 'Line Consumption'],
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
				<div class="rounded-2xl bg-white/40 border border-white/60 p-5 space-y-3 text-sm text-[#3c383d]">
					<p class="font-semibold text-[#1c3a2a]">House Specials</p>
					<ul class="space-y-2 text-[#3c383d]">
						<li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-[#008000]"></span>Chocolate & mango celebration cakes</li>
						<li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-[#008000]"></span>Palabok, siopao, and comforting mami</li>
						<li class="flex items-center gap-3"><span class="h-1.5 w-1.5 rounded-full bg-[#008000]"></span>Empanada and arroz caldo favorites</li>
					</ul>
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
						Demo Access
						<span class="block h-px flex-1 bg-gray-200"></span>
					</div>

					<div class="space-y-3">
						<?php foreach ($demoAccounts as $account): ?>
							<div class="rounded-2xl border border-[#F5E6F0] bg-[#FCBBE9]/20 px-4 py-3">
								<div class="flex items-center justify-between">
									<div>
										<p class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($account['role']); ?></p>
										<p class="text-xs text-gray-500"><?php echo htmlspecialchars($account['note']); ?></p>
									</div>
									<span class="text-[11px] font-semibold uppercase tracking-wide text-[#008000]">Admin@123</span>
								</div>
								<p class="text-xs font-mono text-gray-600 mt-2">Email: <?php echo htmlspecialchars($account['email']); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
		</div>
	</div>
</div>

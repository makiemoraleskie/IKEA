<!-- Login Page Container -->
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
	<div class="sm:mx-auto sm:w-full sm:max-w-md">
		<!-- Logo and Branding -->
		<div class="flex flex-col items-center mb-8">
			<div class="w-12 h-12 border-2 border-blue-600 rounded-lg flex items-center justify-center mb-4">
				<div class="w-6 h-6 bg-blue-600 rounded"></div>
			</div>
			<h1 class="text-3xl font-bold text-gray-900">iKEA</h1>
			<p class="text-sm text-gray-600 mt-1">Inventory Management System</p>
		</div>
	</div>

	<div class="sm:mx-auto sm:w-full sm:max-w-md">
		<!-- Main Login Form -->
		<div class="bg-white py-8 px-6 shadow-lg rounded-lg sm:px-10">
			<h2 class="text-xl font-semibold text-gray-900 mb-6">Sign in to your account</h2>
			
			<?php if (!empty($error)): ?>
				<div class="mb-4 p-3 rounded bg-red-50 text-red-700 text-sm"><?php echo htmlspecialchars($error); ?></div>
			<?php endif; ?>
			
			<?php $baseUrl = defined('BASE_URL') ? BASE_URL : ''; ?>
			<form method="post" action="<?php echo htmlspecialchars($baseUrl); ?>/login" class="space-y-6" novalidate>
				<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(Csrf::token()); ?>">
				
				<div>
					<label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
					<input 
						id="email" 
						name="email" 
						type="email" 
						placeholder="Enter your email"
						class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
						required 
					/>
				</div>
				
				<div>
					<label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
					<input 
						id="password" 
						name="password" 
						type="password" 
						placeholder="Enter your password"
						class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
						required 
					/>
				</div>
				
				<button 
					type="submit"
					class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
				>
					Sign in
				</button>
			</form>
		</div>

		<!-- Demo Accounts -->
		<div class="mt-6 bg-white py-6 px-6 shadow-lg rounded-lg">
			<h3 class="text-lg font-semibold text-gray-900 mb-4">Demo Accounts</h3>
			
			<!-- Owner Account -->
			<div class="mb-4 p-4 border border-gray-200 rounded-lg">
				<div class="flex items-center justify-between mb-2">
					<h4 class="font-semibold text-gray-900">Owner</h4>
					<span class="text-blue-600 text-sm font-medium">Use this account</span>
				</div>
				<p class="text-sm text-gray-700">Email: makiemorales2@gmail.com</p>
				<p class="text-sm text-gray-700">Password: Admin@123</p>
			</div>

			<!-- Manager Account -->
			<div class="mb-4 p-4 border border-gray-200 rounded-lg">
				<div class="flex items-center justify-between mb-2">
					<h4 class="font-semibold text-gray-900">Manager</h4>
					<span class="text-blue-600 text-sm font-medium">Use this account</span>
				</div>
				<p class="text-sm text-gray-700">Email: manager@demo.local</p>
				<p class="text-sm text-gray-700">Password: Admin@123</p>
			</div>

			<!-- Stock Handler Account -->
			<div class="mb-4 p-4 border border-gray-200 rounded-lg">
				<div class="flex items-center justify-between mb-2">
					<h4 class="font-semibold text-gray-900">Stock Handler</h4>
					<span class="text-blue-600 text-sm font-medium">Use this account</span>
				</div>
				<p class="text-sm text-gray-700">Email: stock@demo.local</p>
				<p class="text-sm text-gray-700">Password: Admin@123</p>
			</div>

			<!-- Purchaser Account -->
			<div class="mb-4 p-4 border border-gray-200 rounded-lg">
				<div class="flex items-center justify-between mb-2">
					<h4 class="font-semibold text-gray-900">Purchaser</h4>
					<span class="text-blue-600 text-sm font-medium">Use this account</span>
				</div>
				<p class="text-sm text-gray-700">Email: purchaser@demo.local</p>
				<p class="text-sm text-gray-700">Password: Admin@123</p>
			</div>

			<!-- Kitchen Staff Account -->
			<div class="p-4 border border-gray-200 rounded-lg">
				<div class="flex items-center justify-between mb-2">
					<h4 class="font-semibold text-gray-900">Kitchen Staff</h4>
					<span class="text-blue-600 text-sm font-medium">Use this account</span>
				</div>
				<p class="text-sm text-gray-700">Email: kitchen@demo.local</p>
				<p class="text-sm text-gray-700">Password: Admin@123</p>
			</div>
		</div>
	</div>
</div>



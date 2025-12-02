/**
 * Global UI/UX Utilities for IKEA Commissary System
 * Provides: Loading states, Toast notifications, Confirmation dialogs, Form validation
 */

(function() {
	'use strict';

	// ============================================
	// Toast Notification System
	// ============================================
	const Toast = {
		container: null,
		
		init() {
			if (!this.container) {
				this.container = document.createElement('div');
				this.container.id = 'toast-container';
				this.container.className = 'fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none';
				document.body.appendChild(this.container);
			}
		},
		
		show(message, type = 'info', duration = 5000) {
			this.init();
			const toast = document.createElement('div');
			const id = 'toast-' + Date.now();
			toast.id = id;
			
			const typeClasses = {
				success: 'bg-green-50 border-green-200 text-green-800',
				error: 'bg-red-50 border-red-200 text-red-800',
				warning: 'bg-amber-50 border-amber-200 text-amber-800',
				info: 'bg-blue-50 border-blue-200 text-blue-800'
			};
			
			const icons = {
				success: 'check-circle',
				error: 'alert-circle',
				warning: 'alert-triangle',
				info: 'info'
			};
			
			toast.className = `pointer-events-auto min-w-[300px] max-w-md rounded-lg border shadow-lg p-4 flex items-start gap-3 animate-slide-in-right ${typeClasses[type] || typeClasses.info}`;
			toast.innerHTML = `
				<i data-lucide="${icons[type] || icons.info}" class="w-5 h-5 mt-0.5 flex-shrink-0"></i>
				<div class="flex-1">
					<p class="text-sm font-medium">${this.escapeHtml(message)}</p>
				</div>
				<button type="button" onclick="this.closest('#${id}').remove()" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
					<i data-lucide="x" class="w-4 h-4"></i>
				</button>
			`;
			
			this.container.appendChild(toast);
			if (window.lucide) {
				window.lucide.createIcons();
			}
			
			setTimeout(() => {
				toast.style.transition = 'opacity 0.3s, transform 0.3s';
				toast.style.opacity = '0';
				toast.style.transform = 'translateX(100%)';
				setTimeout(() => toast.remove(), 300);
			}, duration);
		},
		
		escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	};

	// ============================================
	// Loading State Manager
	// ============================================
	const Loading = {
		show(buttonOrForm, text = 'Processing...') {
			if (buttonOrForm instanceof HTMLButtonElement) {
				const btn = buttonOrForm;
				btn.dataset.originalText = btn.innerHTML;
				btn.disabled = true;
				btn.innerHTML = `
					<svg class="animate-spin -ml-1 mr-2 h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					${text}
				`;
			} else if (buttonOrForm instanceof HTMLFormElement) {
				const form = buttonOrForm;
				const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
				if (submitBtn) {
					this.show(submitBtn, text);
				}
				// Disable all form inputs
				Array.from(form.elements).forEach(el => {
					if (el.tagName !== 'BUTTON' || el.type !== 'submit') {
						el.disabled = true;
					}
				});
			}
		},
		
		hide(buttonOrForm) {
			if (buttonOrForm instanceof HTMLButtonElement) {
				const btn = buttonOrForm;
				if (btn.dataset.originalText) {
					btn.innerHTML = btn.dataset.originalText;
					delete btn.dataset.originalText;
				}
				btn.disabled = false;
			} else if (buttonOrForm instanceof HTMLFormElement) {
				const form = buttonOrForm;
				const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
				if (submitBtn) {
					this.hide(submitBtn);
				}
				// Re-enable all form inputs
				Array.from(form.elements).forEach(el => {
					el.disabled = false;
				});
			}
		}
	};

	// ============================================
	// Confirmation Dialog
	// ============================================
	const Confirm = {
		show(message, title = 'Confirm Action', confirmText = 'Confirm', cancelText = 'Cancel', type = 'warning') {
			return new Promise((resolve) => {
				const modal = document.createElement('div');
				modal.className = 'fixed inset-0 bg-black/60 z-[9998] flex items-center justify-center p-4';
				modal.style.animation = 'fadeIn 0.2s';
				
				const typeClasses = {
					danger: 'bg-red-50 border-red-200',
					warning: 'bg-amber-50 border-amber-200',
					info: 'bg-blue-50 border-blue-200'
				};
				
				const icons = {
					danger: 'alert-circle',
					warning: 'alert-triangle',
					info: 'info'
				};
				
				modal.innerHTML = `
					<div class="bg-white rounded-2xl shadow-xl max-w-md w-full border-2 ${typeClasses[type] || typeClasses.warning}" style="animation: slideUp 0.3s">
						<div class="p-6">
							<div class="flex items-start gap-4">
								<div class="flex-shrink-0 w-12 h-12 rounded-full ${type === 'danger' ? 'bg-red-100' : type === 'warning' ? 'bg-amber-100' : 'bg-blue-100'} flex items-center justify-center">
									<i data-lucide="${icons[type] || icons.warning}" class="w-6 h-6 ${type === 'danger' ? 'text-red-600' : type === 'warning' ? 'text-amber-600' : 'text-blue-600'}"></i>
								</div>
								<div class="flex-1">
									<h3 class="text-lg font-semibold text-gray-900 mb-2">${this.escapeHtml(title)}</h3>
									<p class="text-sm text-gray-700">${this.escapeHtml(message)}</p>
								</div>
							</div>
							<div class="mt-6 flex justify-end gap-3">
								<button type="button" class="cancel-btn px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
									${this.escapeHtml(cancelText)}
								</button>
								<button type="button" class="confirm-btn px-4 py-2 text-sm font-medium text-white ${type === 'danger' ? 'bg-red-600 hover:bg-red-700' : type === 'warning' ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700'} rounded-lg transition-colors">
									${this.escapeHtml(confirmText)}
								</button>
							</div>
						</div>
					</div>
				`;
				
				document.body.appendChild(modal);
				if (window.lucide) {
					window.lucide.createIcons();
				}
				
				const close = (result) => {
					modal.style.animation = 'fadeOut 0.2s';
					setTimeout(() => modal.remove(), 200);
					resolve(result);
				};
				
				modal.querySelector('.cancel-btn').addEventListener('click', () => close(false));
				modal.querySelector('.confirm-btn').addEventListener('click', () => close(true));
				modal.addEventListener('click', (e) => {
					if (e.target === modal) close(false);
				});
			});
		},
		
		escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	};

	// ============================================
	// Global Search Functionality
	// ============================================
	const GlobalSearch = {
		init() {
			const searchInput = document.querySelector('header input[type="text"][placeholder*="Search"]');
			if (!searchInput) return;
			
			let searchTimeout;
			searchInput.addEventListener('input', (e) => {
				clearTimeout(searchTimeout);
				const query = e.target.value.trim();
				
				if (query.length < 2) {
					this.hideResults();
					return;
				}
				
				searchTimeout = setTimeout(() => {
					this.performSearch(query);
				}, 300);
			});
			
			searchInput.addEventListener('focus', () => {
				if (searchInput.value.trim().length >= 2) {
					this.performSearch(searchInput.value.trim());
				}
			});
			
			// Close on outside click
			document.addEventListener('click', (e) => {
				if (!searchInput.contains(e.target) && !this.resultsPanel?.contains(e.target)) {
					this.hideResults();
				}
			});
		},
		
		performSearch(query) {
			// Simple client-side search across visible content
			const results = [];
			const lowerQuery = query.toLowerCase();
			
			// Search ingredients
			document.querySelectorAll('[data-searchable]').forEach(el => {
				const text = el.textContent.toLowerCase();
				if (text.includes(lowerQuery)) {
					const link = el.closest('a') || el.querySelector('a');
					if (link) {
						results.push({
							type: el.dataset.searchable || 'item',
							text: el.textContent.trim().substring(0, 60),
							url: link.href
						});
					}
				}
			});
			
			// Search in tables
			document.querySelectorAll('table tbody tr').forEach(row => {
				const text = row.textContent.toLowerCase();
				if (text.includes(lowerQuery)) {
					const link = row.querySelector('a');
					if (link && results.length < 10) {
						results.push({
							type: 'table-row',
							text: row.textContent.trim().substring(0, 60),
							url: link.href
						});
					}
				}
			});
			
			this.showResults(results, query);
		},
		
		showResults(results, query) {
			if (!this.resultsPanel) {
				this.resultsPanel = document.createElement('div');
				this.resultsPanel.id = 'global-search-results';
				this.resultsPanel.className = 'absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-lg shadow-xl z-50 max-h-96 overflow-y-auto';
				const searchInput = document.querySelector('header input[type="text"][placeholder*="Search"]');
				searchInput.parentElement.style.position = 'relative';
				searchInput.parentElement.appendChild(this.resultsPanel);
			}
			
			if (results.length === 0) {
				this.resultsPanel.innerHTML = `
					<div class="p-4 text-center text-sm text-gray-500">
						<i data-lucide="search-x" class="w-5 h-5 mx-auto mb-2 text-gray-400"></i>
						<p>No results found for "${this.escapeHtml(query)}"</p>
					</div>
				`;
			} else {
				this.resultsPanel.innerHTML = `
					<div class="p-2">
						${results.map(r => `
							<a href="${r.url}" class="block px-3 py-2 hover:bg-gray-50 rounded text-sm">
								<div class="font-medium text-gray-900">${this.escapeHtml(r.text)}</div>
								<div class="text-xs text-gray-500 mt-1">${r.type}</div>
							</a>
						`).join('')}
					</div>
				`;
			}
			
			if (window.lucide) {
				window.lucide.createIcons();
			}
		},
		
		hideResults() {
			if (this.resultsPanel) {
				this.resultsPanel.remove();
				this.resultsPanel = null;
			}
		},
		
		escapeHtml(text) {
			const div = document.createElement('div');
			div.textContent = text;
			return div.innerHTML;
		}
	};

	// ============================================
	// Form Auto-Submit with Loading States
	// ============================================
	const FormHandler = {
		init() {
			// Auto-add loading states to all forms
			document.querySelectorAll('form').forEach(form => {
				form.addEventListener('submit', (e) => {
					// Skip if form has data-no-loading attribute
					if (form.dataset.noLoading) return;
					
					const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
					if (submitBtn) {
						Loading.show(submitBtn);
					}
					
					// Re-enable after 10 seconds as fallback (in case of errors)
					setTimeout(() => {
						Loading.hide(form);
					}, 10000);
				});
			});
			
			// Add confirmation dialogs to destructive actions
			document.querySelectorAll('form[data-confirm]').forEach(form => {
				form.addEventListener('submit', async (e) => {
					e.preventDefault();
					const message = form.dataset.confirm;
					const confirmed = await Confirm.show(
						message,
						'Confirm Action',
						'Confirm',
						'Cancel',
						form.dataset.confirmType || 'warning'
					);
					if (confirmed) {
						form.submit();
					}
				});
			});
		}
	};

	// ============================================
	// Mobile Responsiveness Helpers
	// ============================================
	const MobileHelper = {
		init() {
			// Make tables responsive
			document.querySelectorAll('table').forEach(table => {
				if (!table.closest('.overflow-x-auto') && !table.closest('[class*="overflow"]')) {
					const wrapper = document.createElement('div');
					wrapper.className = 'overflow-x-auto -mx-4 sm:mx-0';
					table.parentNode.insertBefore(wrapper, table);
					wrapper.appendChild(table);
				}
			});
			
			// Improve form inputs on mobile
			if (window.innerWidth < 640) {
				document.querySelectorAll('input[type="text"], input[type="number"], select, textarea').forEach(input => {
					input.classList.add('text-base'); // Prevent zoom on iOS
				});
			}
			
			// Make modals more mobile-friendly
			document.querySelectorAll('[class*="modal"], [id*="Modal"]').forEach(modal => {
				if (window.innerWidth < 640) {
					modal.classList.add('max-h-[90vh]', 'overflow-y-auto');
				}
			});
		}
	};

	// ============================================
	// Form UX Improvements (Autocomplete, Calculators)
	// ============================================
	const FormUX = {
		init() {
			// Add autocomplete to ingredient name inputs
			document.querySelectorAll('input[name="name"][placeholder*="ingredient" i], input[name="item_name"]').forEach(input => {
				input.setAttribute('autocomplete', 'off');
				input.setAttribute('list', 'ingredient-suggestions');
			});
			
			// Create datalist for ingredient suggestions
			const datalist = document.createElement('datalist');
			datalist.id = 'ingredient-suggestions';
			document.body.appendChild(datalist);
			
			// Populate from visible ingredient names
			document.querySelectorAll('[data-ingredient-name], td:first-child').forEach(el => {
				const name = el.textContent.trim();
				if (name && name.length > 2) {
					const option = document.createElement('option');
					option.value = name;
					datalist.appendChild(option);
				}
			});
			
			// Quantity calculator helpers
			document.querySelectorAll('input[type="number"][name*="quantity" i], input[type="number"][name*="qty" i]').forEach(input => {
				// Add step helpers
				const wrapper = input.parentElement;
				if (wrapper && !wrapper.querySelector('.qty-helpers')) {
					const helpers = document.createElement('div');
					helpers.className = 'qty-helpers flex gap-1 mt-1';
					helpers.innerHTML = `
						<button type="button" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded" data-action="add" data-value="1">+1</button>
						<button type="button" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded" data-action="add" data-value="10">+10</button>
						<button type="button" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded" data-action="multiply" data-value="2">×2</button>
					`;
					wrapper.appendChild(helpers);
					
					helpers.querySelectorAll('button').forEach(btn => {
						btn.addEventListener('click', () => {
							const current = parseFloat(input.value) || 0;
							const action = btn.dataset.action;
							const value = parseFloat(btn.dataset.value);
							if (action === 'add') {
								input.value = current + value;
							} else if (action === 'multiply') {
								input.value = current * value;
							}
							input.dispatchEvent(new Event('input', { bubbles: true }));
						});
					});
				}
			});
			
			// Smart defaults for date inputs
			document.querySelectorAll('input[type="date"]').forEach(input => {
				if (!input.value && input.name.includes('date')) {
					input.value = new Date().toISOString().split('T')[0];
				}
			});
		}
	};

	// ============================================
	// Initialize Everything
	// ============================================
	document.addEventListener('DOMContentLoaded', () => {
		FormHandler.init();
		GlobalSearch.init();
		MobileHelper.init();
		FormUX.init();
		
		// Show flash messages as toasts
		const flashMessages = document.querySelectorAll('[class*="flash"], [class*="border-red"], [class*="border-green"]');
		flashMessages.forEach(msg => {
			const text = msg.textContent.trim();
			if (text) {
				const isError = msg.classList.toString().includes('red') || msg.classList.toString().includes('error');
				const isSuccess = msg.classList.toString().includes('green') || msg.classList.toString().includes('success');
				Toast.show(text, isError ? 'error' : isSuccess ? 'success' : 'info', 6000);
			}
		});
	});

	// ============================================
	// Export to Global Scope
	// ============================================
	window.Toast = Toast;
	window.Loading = Loading;
	window.Confirm = Confirm;
	window.GlobalSearch = GlobalSearch;

	// Add CSS animations
	const style = document.createElement('style');
	style.textContent = `
		@keyframes fadeIn {
			from { opacity: 0; }
			to { opacity: 1; }
		}
		@keyframes fadeOut {
			from { opacity: 1; }
			to { opacity: 0; }
		}
		@keyframes slideUp {
			from { transform: translateY(20px); opacity: 0; }
			to { transform: translateY(0); opacity: 1; }
		}
		@keyframes slide-in-right {
			from { transform: translateX(100%); opacity: 0; }
			to { transform: translateX(0); opacity: 1; }
		}
	`;
	document.head.appendChild(style);
})();

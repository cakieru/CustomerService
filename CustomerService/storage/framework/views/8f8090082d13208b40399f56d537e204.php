

<?php $__env->startSection('content'); ?>
<div class="text-sm text-gray-500 mb-6 font-medium">
    <a href="<?php echo e(route('CustomerPortal')); ?>" class="hover:text-gray-900">🏠 Home</a> > <span class="text-gray-900">New Request</span>
</div>

<h2 class="text-3xl font-bold text-gray-900 mb-2">Submit a support request</h2>
<p class="text-gray-500 mb-8">Fill in the details below and we'll get back to you as soon as possible.</p>

<div class="flex flex-col lg:flex-row gap-8">
    
    <div class="flex-grow space-y-6">
        <!-- ENCTYPE ADDED HERE -->
        <form action="<?php echo e(route('tickets.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6">
                <h3 class="font-bold text-gray-900 mb-4 tracking-wide text-sm">YOUR INFORMATION</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <input type="text" name="name" id="name" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="Enter your full name" required>
                    </div>

                    <div>
                        <input type="email" name="email" id="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent" placeholder="jane06@example.com" required>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Number <span class="text-gray-400 font-normal">(optional)</span></label>
                    <input type="text" name="order_number" placeholder="e.g ORD-1234" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none">
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6">
                <h3 class="font-bold text-gray-900 mb-4 tracking-wide text-sm">REQUEST DETAILS</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <div class="relative">
                        <select name="category" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 outline-none appearance-none bg-white" required>
                            <option value="">Select a Category...</option>
                            <option value="Subscription">Subscription</option>
                            <option value="Shipping & Delivery">Shipping & Delivery</option>
                            <option value="Returns & Refunds">Returns & Refunds</option>
                            <option value="Account Management">Account Management</option>
                            <option value="Damaged Product">Damaged Product</option>
                            <option value="Product Information">Product Information</option>
                            <option value="Promotion & Discounts">Promotion & Discounts</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                    <input type="text" name="subject" placeholder="Brief Summary of your issue" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 outline-none" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <textarea name="description" rows="5" placeholder="Please describe your issue in detail..." class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 outline-none resize-none" required></textarea>
                </div>

                <!-- ATTACHMENT UPLOAD BLOCK -->
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                    <label class="flex flex-col items-center justify-center w-full px-4 py-8 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer hover:bg-gray-50 hover:border-blue-400 transition-all group">
                        <div class="flex flex-col items-center space-y-2 text-center">
                            <svg class="w-8 h-8 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <span class="text-sm text-gray-500 font-medium">Click to upload files</span>
                            <span class="text-xs text-gray-400">PNG, JPG, PDF, DOC up to 10MB each</span>
                        </div>
                        <input type="file" name="attachments[]" multiple class="hidden" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    </label>
                    <!-- Live preview of selected files -->
                    <div id="filePreview" class="mt-3 space-y-2"></div>
                </div>
            </div>

            <button type="submit" class="w-full bg-[#2B2D85] hover:bg-indigo-900 text-white font-medium py-3.5 rounded-xl shadow-sm transition flex justify-center items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <span>Submit Support Request</span>
            </button>
        </form>
    </div>

    <div class="w-full lg:w-80 space-y-6">
        
        <div class="bg-blue-50/50 border border-blue-100 p-6 rounded-2xl">
            <div class="flex items-center space-x-2 text-blue-800 font-bold mb-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Before You Submit</span>
            </div>
            <p class="text-sm text-blue-700/80 mb-4">Check if your question is already answered in our knowledge base or check our response SLAs below.</p>
            
            <div class="space-y-3 mt-4 border-t border-blue-200 pt-4">
                <div class="flex justify-between items-center">
                    <span class="bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded text-xs">High</span>
                    <span class="text-gray-600 font-medium text-sm">&lt; 4 Hours</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="bg-yellow-100 text-yellow-700 font-bold px-2 py-0.5 rounded text-xs">Medium</span>
                    <span class="text-gray-600 font-medium text-sm">&lt; 8 Hours</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="bg-gray-100 text-gray-600 font-bold px-2 py-0.5 rounded text-xs">Low</span>
                    <span class="text-gray-600 font-medium text-sm">&lt; 24 Hours</span>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-sm text-center">
            <div class="text-[#0ea5e9] mb-3 flex justify-center">
                <svg class="w-10 h-10 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7c0 1.434-.493 2.767-1.338 3.123L18 17l-1.917-2.98A8.841 8.841 0 0118 10z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <h4 class="font-bold text-xl text-gray-900 mb-2">Need quick help?</h4>
            <p class="text-sm text-gray-500 mb-5 leading-relaxed">Search our knowledge base for instant answers to common questions.</p>
        </div>

    </div>
</div>

<script>
    // Live file preview
    document.querySelector('input[name="attachments[]"]').addEventListener('change', function(e) {
        const preview = document.getElementById('filePreview');
        preview.innerHTML = '';
        Array.from(e.target.files).forEach(file => {
            const size = (file.size / 1024 / 1024).toFixed(2);
            const div = document.createElement('div');
            div.className = 'flex items-center gap-3 text-xs text-gray-700 bg-gray-50 px-3 py-2.5 rounded-lg border border-gray-200';
            div.innerHTML = `
                <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                </svg>
                <span class="font-medium truncate">${file.name}</span>
                <span class="text-gray-400 ml-auto flex-shrink-0">${size} MB</span>
            `;
            preview.appendChild(div);
        });
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Administrator\Documents\GitHub\CustomerService\CustomerService\resources\views/customer/create.blade.php ENDPATH**/ ?>
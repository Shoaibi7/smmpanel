<!-- Provider Modal Redesigned -->
<div id="providerModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-secondary-950/80 backdrop-blur-sm hidden animate-in fade-in duration-300">
    <div class="bg-white dark:bg-secondary-900 rounded-[2rem] shadow-2xl w-full max-w-lg p-0 overflow-hidden transform transition-all scale-95 opacity-0 duration-300 border border-secondary-100 dark:border-secondary-800" id="providerModalContainer">
        
        <!-- Modal Header -->
        <div class="bg-secondary-50 dark:bg-secondary-950 px-8 py-5 border-b border-secondary-100 dark:border-secondary-800 flex justify-between items-center">
            <h3 class="text-lg font-black text-secondary-900 dark:text-white flex items-center tracking-tighter uppercase">
                <span class="w-9 h-9 rounded-xl bg-orange-100 dark:bg-orange-900/30 text-orange-600 flex items-center justify-center mr-3 shadow-sm shadow-orange-500/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </span>
                <span id="providerModalTitle" class="tracking-tight">NEW API PROVIDER</span>
            </h3>
            <button onclick="closeProviderModal()" class="w-8 h-8 flex items-center justify-center rounded-xl bg-secondary-100 dark:bg-secondary-800 text-secondary-500 hover:text-red-500 transition-all active:scale-90">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Modal Body -->
        <div class="max-h-[85vh] overflow-y-auto custom-scrollbar">
            <form id="providerForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="provider_id" id="provider_id" value="">
                
                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="col-span-1 text-left">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">Panel Name</label>
                        <input type="text" name="api_name" id="api_name" required placeholder="e.g. Dilsmm"
                            class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        <div class="text-[8px] font-bold text-red-500 mt-1 ml-1" id="error_api_name"></div>
                    </div>

                    <div class="col-span-1 text-left">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">Short Code</label>
                        <input type="text" name="short_name" id="short_name" required placeholder="e.g. DIL"
                            class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                         <div class="text-[8px] font-bold text-red-500 mt-1 ml-1" id="error_short_name"></div>
                    </div>
                </div>

                <div class="text-left">
                    <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">API Endpoint URL</label>
                    <input type="url" name="api_url" id="api_url" required placeholder="https://example.com/api/v2"
                        class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                    <div class="text-[8px] font-bold text-red-500 mt-1 ml-1" id="error_api_url"></div>
                </div>

                <div class="text-left">
                    <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">Private API Key</label>
                    <div class="relative">
                        <input type="password" name="api_key" id="api_key" placeholder="Enter security token"
                            class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-secondary-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                    </div>
                    <div class="text-[8px] font-bold text-red-500 mt-1 ml-1" id="error_api_key"></div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-left">
                    <div class="col-span-1">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">Base Currency</label>
                        <input type="text" name="currency" id="currency" placeholder="USD"
                            class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none uppercase">
                    </div>
                    <div class="col-span-1">
                        <label class="block text-[9px] font-black text-secondary-400 uppercase tracking-widest mb-1 ml-1">Working Status</label>
                        <select name="status" id="status" class="w-full px-4 py-1.5 text-xs border border-secondary-100 dark:border-secondary-800 rounded-xl bg-secondary-50 dark:bg-secondary-950 dark:text-white font-bold focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 transition-all outline-none">
                            <option value="enabled">Active (Online)</option>
                            <option value="disabled">Paused (Offline)</option>
                        </select>
                    </div>
                </div>

                <!-- Modal Footer Buttons -->
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeProviderModal()" class="flex-1 px-4 py-3 rounded-2xl border border-secondary-100 dark:border-secondary-800 text-secondary-500 font-black text-[9px] uppercase tracking-widest hover:bg-secondary-50 dark:hover:bg-secondary-800 transition-all active:scale-95">
                        Discard
                    </button>
                    <button type="submit" id="providerModalSubmit" class="flex-[1.5] px-4 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-2xl font-black text-[9px] uppercase tracking-widest shadow-xl shadow-orange-500/20 active:scale-95 transition-all">
                        SAVE PROVIDER
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.openCreateModal = function() {
        document.getElementById('providerForm').reset();
        document.getElementById('provider_id').value = '';
        document.getElementById('providerModalTitle').textContent = 'NEW API PROVIDER';
        document.getElementById('providerModalSubmit').textContent = 'CONNECT PROVIDER';
        
        // Clear errors
        ['api_name','short_name','api_url','api_key'].forEach(id => {
            const el = document.getElementById('error_' + id);
            if (el) el.textContent = '';
        });

        $('#providerModal').removeClass('hidden');
        setTimeout(() => $('#providerModalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    };

    window.openEditModal = function(btn) {
        document.getElementById('provider_id').value = btn.dataset.id;
        document.getElementById('api_name').value = btn.dataset.api_name || '';
        document.getElementById('short_name').value = btn.dataset.short_name || '';
        document.getElementById('api_url').value = btn.dataset.api_url || '';
        document.getElementById('api_key').value = ''; 
        document.getElementById('currency').value = btn.dataset.currency || 'USD';
        document.getElementById('status').value = btn.dataset.status || 'enabled';
        
        document.getElementById('providerModalTitle').textContent = 'MODIFY PROVIDER';
        document.getElementById('providerModalSubmit').textContent = 'UPDATE CONNECTION';

        $('#providerModal').removeClass('hidden');
        setTimeout(() => $('#providerModalContainer').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    };

    window.closeProviderModal = function() {
        $('#providerModalContainer').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');
        setTimeout(() => $('#providerModal').addClass('hidden'), 300);
    };

    $('#providerForm').on('submit', function(e) {
        e.preventDefault();
        const providerId = $('#provider_id').val();
        const $btn = $('#providerModalSubmit');
        const originalText = $btn.text();

        $btn.prop('disabled', true).text('PROCESSING...');
        
        let url = providerId ? `/admin/api-providers/${providerId}` : '/admin/api-providers';
        let data = $(this).serialize();
        if (providerId) data += '&_method=PUT';

        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            success: function(res) {
                closeProviderModal();
                showToast(res.message || 'Success!', 'success');
                setTimeout(() => location.reload(), 800);
            },
            error: function(xhr) {
                $btn.prop('disabled', false).text(originalText);
                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(key => {
                        $(`#error_${key}`).text(errors[key][0]);
                    });
                } else {
                    showToast(xhr.responseJSON?.message || 'Error occurred', 'error');
                }
            }
        });
    });
</script>
